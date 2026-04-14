<?php

namespace App\Livewire\Menus;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\Society;
use App\Models\SocietyDetail;
use App\Models\Timeline;
use App\Models\State;
use App\Models\City;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Exports\ApartmentExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminDashboard extends Component
{
    use WithPagination, WithFileUploads;
    public $societies, $societyDetails = [];
    public $apartments = [];
    public $userRole;
    public $filterCounts = [];
    public $selectedSocietyId, $societyName, $filterId, $societyById;
    public $societyId = 0;
    public $filterKey = 0;
    public $search = '';
    public $timelines, $timelineValues;
    public $pendingVerificationTimelineId = 0;
    public $pendingApplicationTimelineId = 0;
    public $showAssignModal = false;
    public $showEditSocietyModal = false;
    public $step = 1;
    public $no_of_shares, $share_value, $individual_no_of_share, $share_capital_amount;
    public $assignType = null;

    public $states, $cities = [];
    public $edit_society_name, $edit_address_1, $edit_address_2, $edit_pincode, $edit_state_id, $edit_city_id;
    public $edit_total_building, $edit_total_flats, $edit_registration_no, $edit_no_of_shares, $edit_share_value;
    public $edit_is_list_of_signed_member_available = 'No';
    public $edit_is_byelaws_available = 'No';

    public $signedMembersFile;
    public $signedMembersMessage = '';
    public $showSignedMembersUploader = false;

    public function render()
    {
        $user = Auth::user();
        $societiesQuery = Society::with(['state', 'city']);

        if ($this->search) {
            $societiesQuery->where('society_name', 'like', '%' . $this->search . '%');
        }

        $this->societies = $societiesQuery->where('admin_id', $user->id)->get()->map(function ($society) {
            $society->changes_required_count = SocietyDetail::where('society_id', $society->id)
                ->where('certificate_status', 'changes_required')
                ->count();
            return $society;
        });

        return view('livewire.menus.admin-dashboard');
    }

    public function mount()
    {
        $this->states = State::orderBy('name', 'asc')->get();
        $this->timelines = Timeline::where('id', '!=', 1)->get();
        // Store the Pending Verification timeline id for default filter
        $verificationTimeline = Timeline::where('name', 'like', '%Verification%')->first();
        $this->pendingVerificationTimelineId = $verificationTimeline ? $verificationTimeline->id : 0;

        $applicationTimeline = Timeline::where('name', 'like', '%Application%')->first();
        $this->pendingApplicationTimelineId = $applicationTimeline ? $applicationTimeline->id : 0;

        $this->userRole = Role::where('role', 'Society User')->value('id');
        // Select first assigned society by default
        $firstSociety = Society::where('admin_id', Auth::id())->first();
        if ($firstSociety) {
            $this->selectSociety($firstSociety->id);
        }
    }

    public function selectSociety($societyId)
    {
        $this->selectedSocietyId = $societyId;
        $this->societyById = Society::with(['state', 'city', 'admin'])->find($societyId);
        $this->societyName = $this->societyById->society_name;

        // Default to Pending Verification filter
        $this->filterKey = $this->pendingVerificationTimelineId;
        $this->filterId = $societyId;

        $this->calculateFilterCounts($societyId);
    }

    public function refreshCounts()
    {
        if ($this->selectedSocietyId) {
            $this->calculateFilterCounts($this->selectedSocietyId);
        }
    }

    public function calculateFilterCounts($societyId)
    {
        $details = SocietyDetail::where('society_id', $societyId)->get();
        $this->filterCounts = [];

        // Fetch timelines for dependency logic (matching SocietyStepper)
        $timelines = Timeline::where('id', '!=', 1)->orderBy('id')->get();
        $dependencies = [];
        $previousSteps = [];
        $idToName = [];
        foreach ($timelines as $timeline) {
            $idToName[$timeline->id] = $timeline->name;
            $dependencies[$timeline->name] = $previousSteps;
            $previousSteps[] = $timeline->name;
        }

        // Count for "All" (Pending any task)
        $this->filterCounts[0] = $details->filter(function ($item) {
            $json = json_decode($item->status, true);
            if (!isset($json['tasks'])) return false;
            return collect($json['tasks'])->contains(fn($t) => ($t['Status'] ?? null) === 'Pending');
        })->count();

        // Count for "Pending"
        $this->filterCounts['pendingCertificateStatus'] = $details->where('certificate_status', 'pending')->filter(function ($item) {
            $json = json_decode($item->status, true);
            if (!isset($json['tasks'])) {
                return false;
            }
            $tasks = collect($json['tasks']);
            return $tasks->every(function ($task) {
                if (($task['name'] ?? '') === 'Certificate Delivered') {
                    return ($task['Status'] ?? '') === 'Pending';
                }
                return ($task['Status'] ?? '') === 'Approved';
            });
        })->count();

        // Count for "Changes Required"
        $this->filterCounts['changedCertificateStatus'] = $details->where('certificate_status', 'changes_required')->count();

        // Count for each timeline
        foreach ($timelines as $timeline) {
            $currentStep = $timeline->name;
            $this->filterCounts[$timeline->id] = $details->filter(function ($item) use ($currentStep, $dependencies) {

                // NEW: Specialized logic for "Certificate Delivered"
                if ($currentStep === 'Certificate Delivered') {
                    if ($item->certificate_status !== 'approved') {
                        return false;
                    }
                }

                $json = json_decode($item->status, true);
                if (!isset($json['tasks'])) return false;
                $tasks = collect($json['tasks'])->keyBy('name');

                // Dependency check (matching SocietyStepper)
                foreach ($dependencies[$currentStep] ?? [] as $dep) {
                    if (($tasks[$dep]['Status'] ?? null) !== 'Approved') {
                        return false;
                    }
                }
                return ($tasks[$currentStep]['Status'] ?? null) === 'Pending';
            })->count();
        }
    }

    public function redirectToCreateSociety()
    {
        return redirect()->route('menus.create_society');
    }

    public function redirectToCreateApartment()
    {
        return redirect()->route('menus.create_apartment');
    }

    public function markRoleByAdmin()
    {
        return redirect()->route('menus.mark_role');
    }

    public function setFilter($societyId, $key)
    {
        $this->filterId = $societyId;
        $this->filterKey = $key;
    }

    public function assignShareToApartment($societyId)
    {
        $this->selectedSocietyId = $societyId;
        $this->societyById = Society::find($societyId);
        $this->no_of_shares = $this->societyById->no_of_shares;
        $this->share_value = $this->societyById->share_value;
        // if ($this->societyById && $this->societyById->no_of_shares && $this->societyById->share_value) {
        //     $this->step = 2; // skip to next form
        // } else {
        //     $this->step = 1; // show initial form
        // }
        $this->showAssignModal = true;
    }

    public function editSociety()
    {
        if (!$this->selectedSocietyId) {
            $this->dispatch('show-error', message: 'Please select a society first.');
            return;
        }

        $this->societyById = Society::with(['state', 'city'])->find($this->selectedSocietyId);
        if (!$this->societyById) {
            $this->dispatch('show-error', message: 'Selected society not found.');
            return;
        }

        $this->edit_society_name = $this->societyById->society_name;
        $this->edit_registration_no = $this->societyById->registration_no;
        $this->edit_total_building = $this->societyById->total_building;
        $this->edit_total_flats = $this->societyById->total_flats;
        $this->edit_address_1 = $this->societyById->address_1;
        $this->edit_address_2 = $this->societyById->address_2;
        $this->edit_pincode = $this->societyById->pincode;
        $this->edit_state_id = $this->societyById->state_id;
        $this->cities = City::where('state_id', $this->edit_state_id)->get();
        $this->edit_city_id = $this->societyById->city_id;
        $this->edit_no_of_shares = $this->societyById->no_of_shares;
        $this->edit_share_value = $this->societyById->share_value;
        $this->edit_is_list_of_signed_member_available = $this->societyById->is_list_of_signed_member_available ?? 'No';
        $this->edit_is_byelaws_available = $this->societyById->is_byelaws_available ?? 'No';

        $this->updatedEditIsListOfSignedMemberAvailable($this->edit_is_list_of_signed_member_available);

        $this->showEditSocietyModal = true;
    }

    public function updatedEditStateId($value)
    {
        $this->cities = City::where('state_id', $value)->get();
        $this->edit_city_id = '';
    }

    public function updatedEditIsListOfSignedMemberAvailable($value)
    {
        if ($value === 'Yes' && $this->selectedSocietyId) {
            $society = Society::find($this->selectedSocietyId);
            $totalFlats = $society->total_flats;
            $countSigned = SocietyDetail::where('society_id', $this->selectedSocietyId)->count();

            if ($countSigned > $totalFlats) {
                $this->signedMembersMessage = "There are more signed entries ({$countSigned}) than total flats ({$totalFlats}). Please review the data.";
                $this->showSignedMembersUploader = false;
            } elseif ($countSigned < $totalFlats) {
                $remaining = $totalFlats - $countSigned;
                $this->signedMembersMessage = "There are {$countSigned} signed entries out of {$totalFlats} total flats.\nPlease upload the remaining {$remaining} entries.";
                $this->showSignedMembersUploader = true;
            } else {
                $this->signedMembersMessage = "All {$totalFlats} flats have signed entries.";
                $this->showSignedMembersUploader = false;
            }
        } else {
            $this->signedMembersMessage = '';
            $this->showSignedMembersUploader = false;
        }
    }

    public function saveSocietyChanges()
    {
        $validated = $this->validate([
            'edit_society_name' => 'required|string|max:255',
            'edit_total_building' => 'required|numeric',
            'edit_total_flats' => 'required|numeric|min:1',
            'edit_address_1' => 'required|string|max:1000',
            'edit_address_2' => 'nullable|string|max:255',
            'edit_pincode' => 'required|numeric|digits:6',
            'edit_state_id' => 'required|exists:states,id',
            'edit_city_id' => 'required|exists:cities,id',
            'edit_registration_no' => 'required|string|max:255',
            'edit_no_of_shares' => 'required|numeric|min:1',
            'edit_share_value' => 'required|numeric|decimal:0,2',
            'edit_is_list_of_signed_member_available' => 'required|in:Yes,No',
            'edit_is_byelaws_available' => 'required|in:Yes,No',
        ]);

        $society = Society::find($this->selectedSocietyId);
        if (!$society) {
            $this->dispatch('show-error', message: 'Society not found.');
            return;
        }

        // Check if total flats is less than existing society details count
        $currentDetailsCount = SocietyDetail::where('society_id', $this->selectedSocietyId)->count();
        if ($this->edit_total_flats < $currentDetailsCount) {
            $this->dispatch('show-error', message: "Total flats cannot be less than the current number of society details entries ({$currentDetailsCount}).");
            return;
        }

        $society->update([
            'society_name' => $this->edit_society_name,
            'registration_no' => $this->edit_registration_no,
            'total_building' => $this->edit_total_building,
            'total_flats' => $this->edit_total_flats,
            'address_1' => $this->edit_address_1,
            'address_2' => $this->edit_address_2,
            'pincode' => $this->edit_pincode,
            'state_id' => $this->edit_state_id,
            'city_id' => $this->edit_city_id,
            'no_of_shares' => $this->edit_no_of_shares,
            'share_value' => $this->edit_share_value,
            'is_list_of_signed_member_available' => $this->edit_is_list_of_signed_member_available,
            'is_byelaws_available' => $this->edit_is_byelaws_available,
        ]);

        $this->dispatch('show-success', message: 'Society updated successfully!');
        $this->selectSociety($this->selectedSocietyId);
        $this->closeEditSocietyModal();
    }

    public function excelExport()
    {
        $society = Society::find($this->selectedSocietyId);

        $columns = [
            'Building Name',
            'Apartment Number',
            'Certificate No',
        ];

        if ($society && $society->is_list_of_signed_member_available == 'Yes') {
            $columns = array_merge($columns, [
                'Did you purchase the apartment before the society was registered?',
                'Did you sign at the time of the society registration?',
                'Did the previous owner sign the registration documents?',
                'Has the flat transfer-related fee been paid to the Society?',
                'Have physical documents been submitted to the society?'
            ]);
        }

        $columns = array_merge($columns, [
            'Owner1 First Name',
            'Owner1 Middle Name',
            'Owner1 Last Name',
            'Owner1 Mobile',
            'Owner1 Email',
            'Owner2 First Name',
            'Owner2 Middle Name',
            'Owner2 Last Name',
            'Owner2 Mobile',
            'Owner2 Email',
            'Owner3 First Name',
            'Owner3 Middle Name',
            'Owner3 Last Name',
            'Owner3 Mobile',
            'Owner3 Email',
        ]);

        return Excel::download(new ApartmentExport($columns), 'sample_society_details.xlsx');
    }

    public function uploadSignedMembers()
    {
        // Clear previous error states and messages
        $this->resetErrorBag();
        $this->signedMembersMessage = '';
        $this->resetValidation();

        $this->validate([
            'signedMembersFile' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            if (!$this->signedMembersFile) {
                $this->dispatch('show-error', message: 'Please select Excel file');
                return;
            }
            $rows = Excel::toArray([], $this->signedMembersFile);
            $data = $rows[0]; // first sheet

            if (empty($data)) {
                $this->dispatch('show-error', message: 'Empty Excel file');
                return;
            }

            $header = array_map(function ($col) {
                $col = strtolower(trim($col));                 // lowercase
                $col = preg_replace('/[^a-z0-9]+/', '_', $col); // replace special chars + spaces with _
                $col = trim($col, '_');                        // remove starting/ending _
                return $col;
            }, $data[0]);
            unset($data[0]); // remove header
            $this->processData($header, array_values($data));
        } catch (\Exception $e) {
            log::info("Error processing signed members Excel: " . $e->getMessage());
            $this->dispatch('show-error', message: 'Something went wrong while processing the Excel file. Please ensure it is in the correct format and try again.');
        }
    }

    private function processData($header, $rows)
    {
        $requiredHeaders = ['building_name', 'apartment_number', 'certificate_no'];
        $headerMap = array_map('trim', $header);

        foreach ($requiredHeaders as $required) {
            if (!in_array($required, $headerMap)) {
                $this->dispatch('show-error', message: "Missing column: {$required}");
                return;
            }
        }

        $indexes = array_flip($headerMap);

        $idxSigned1 = $indexes['did_you_purchase_the_apartment_before_the_society_was_registered'] ?? $indexes['did_you_purchase_the_apartment_before_the_society_was_registered'] ?? null;
        $idxSigned2 = $indexes['did_you_sign_at_the_time_of_the_society_registration'] ?? $indexes['did_you_sign_at_the_time_of_the_society_registration'] ?? null;
        $idxSigned3 = $indexes['did_the_previous_owner_sign_the_registration_documents'] ?? $indexes['did_the_previous_owner_sign_the_registration_documents'] ?? null;
        $idxSigned4 = $indexes['has_the_flat_transfer_related_fee_been_paid_to_the_society'] ?? $indexes['has_the_flat_transfer_related_fee_been_paid_to_the_society'] ?? null;
        $idxSigned5 = $indexes['have_physical_documents_been_submitted_to_the_society'] ?? $indexes['have_physical_documents_been_submitted_to_the_society'] ?? null;

        $validRows = [];
        $invalidRows = [];
        $rowNumber = 2;

        $society = Society::find($this->selectedSocietyId);
        $expectedFlats = (int) $society->total_flats;
        $existingCount = SocietyDetail::where('society_id', $this->selectedSocietyId)->count();
        $remainingFlats = $expectedFlats - $existingCount;

        if ($remainingFlats <= 0) {
            $this->dispatch('show-error', message: "All {$expectedFlats} flats are already uploaded for this society. Import not allowed.");
            return;
        }
        $hasUnwantedSignedMemberData = false;
        $missingSignedRows = [];
        $missingOwnerRows = [];
        $invalidOwnerRows = [];
        $invalidSignedValueRows = [];
        $errors = [];
        $existingFlats = SocietyDetail::get()
            ->map(function ($item) {
                return strtolower(trim($item->building_name)) . '|' . strtolower(trim($item->apartment_number));
            })
            ->toArray();

        $fileFlats = []; // for duplicate check inside file
        $duplicateFlatErrors = [];
        $allowedSignedValues = ['yes', 'no', 'Yes', 'No', 'होय', 'नाही'];

        foreach ($rows as $i => $data) {
            log::info("Processing row {$rowNumber}: " . json_encode($data));
            $rowNo1 = $i + 2;
            $buildingName = $data[$indexes['building_name']] ?? null;
            $apartmentNumber = $data[$indexes['apartment_number']] ?? null;
            $certificateNo = $data[$indexes['certificate_no']] ?? null;
            $building = strtolower(trim((string)$buildingName));
            $apartment = strtolower(trim((string)$apartmentNumber));
            $key = $building . '|' . $apartment;

            // Check duplicate inside FILE
            if (in_array($key, $fileFlats)) {
                $duplicateFlatErrors[] = "Row {$rowNo1}: Duplicate flat '{$buildingName} - {$apartmentNumber}' in file";
            } else {
                $fileFlats[] = $key;
            }

            // Check duplicate in DATABASE
            if (in_array($key, $existingFlats)) {
                $duplicateFlatErrors[] = "Row {$rowNo1}: Flat '{$buildingName} - {$apartmentNumber}' already exists";
            }

            $signedCol1 = $idxSigned1 !== null ? trim((string)($data[$idxSigned1] ?? '')) : '';
            $signedCol2 = $idxSigned2 !== null ? trim((string)($data[$idxSigned2] ?? '')) : '';
            $signedCol3 = $idxSigned3 !== null ? trim((string)($data[$idxSigned3] ?? '')) : '';
            $signedCol4 = $idxSigned4 !== null ? trim((string)($data[$idxSigned4] ?? '')) : '';
            $signedCol5 = $idxSigned5 !== null ? trim((string)($data[$idxSigned5] ?? '')) : '';

            log::info("Signed member columns for row {$rowNo1}: Col1='{$signedCol1}', Col2='{$signedCol2}', Col3='{$signedCol3}', Col4='{$signedCol4}', Col5='{$signedCol5}'");

            // Validate signed member column values (only Yes/No/होय/नाही allowed)
            if ($society->is_list_of_signed_member_available == 'Yes') {
                $signedColumnsWithValues = [
                    'Did you purchase the apartment before the society was registered?' => $signedCol1,
                    'Did you sign at the time of the society registration?' => $signedCol2,
                    'Did the previous owner sign the registration documents?' => $signedCol3,
                    'Has the flat transfer-related fee been paid to the Society?' => $signedCol4,
                    'Have physical documents been submitted to the society?' => $signedCol5,
                ];

                foreach ($signedColumnsWithValues as $columnName => $value) {
                    if (!empty($value) && !in_array($value, $allowedSignedValues)) {
                        $invalidSignedValueRows[$rowNo1][] = "{$columnName} has invalid value '{$value}'. Allowed values: yes,no,Yes, No, होय, नाही";
                    }
                }
            }
            // Normalize Yes/No

            // Owner fields
            $owner1 = !empty(trim($data[$indexes['owner1_first_name']] ?? '')) && !empty(trim($data[$indexes['owner1_mobile']] ?? ''));
            $owner2 = !empty(trim($data[$indexes['owner2_first_name']] ?? '')) && !empty(trim($data[$indexes['owner2_mobile']] ?? ''));
            $owner3 = !empty(trim($data[$indexes['owner3_first_name']] ?? '')) && !empty(trim($data[$indexes['owner3_mobile']] ?? ''));

            if ($society->is_list_of_signed_member_available == 'Yes') {
                log::info("Checking required signed member data in row {$rowNo1} since society requires it. Owner presence: Owner1={$owner1}, Owner2={$owner2}, Owner3={$owner3}");
                // All 5 signed columns must be present (not empty)
                if ($signedCol1 === '' || $signedCol2 === '' || $signedCol3 === '' || $signedCol4 === '' || $signedCol5 === '') {
                    $missingSignedRows[] = $rowNo1;
                }

                if (!$owner1 && !$owner2 && !$owner3) {
                    $missingOwnerRows[] = $rowNo1;
                }
            }

            if ($society->is_list_of_signed_member_available == 'No') {
                log::info("Checking unwanted signed member data in row {$rowNo1}: Col1='{$signedCol1}', Col2='{$signedCol2}', Col3='{$signedCol3}', Col4='{$signedCol4}', Col5='{$signedCol5}'");
                if ($signedCol1 !== '' || $signedCol2 !== '' || $signedCol3 !== '' || $signedCol4 !== '' || $signedCol5 !== '') {
                    $hasUnwantedSignedMemberData = true;
                }
            }

            foreach ([1, 2, 3] as $i) {
                $name = trim($data[$indexes["owner{$i}_first_name"]] ?? '');
                $mobile = trim($data[$indexes["owner{$i}_mobile"]] ?? '');
                $email = trim($data[$indexes["owner{$i}_email"]] ?? '');

                //Name & Mobile mismatch
                if (
                    (!empty($name) && empty($mobile)) ||
                    (empty($name) && !empty($mobile))
                ) {
                    $invalidOwnerRows[] = $rowNo1;
                }

                // Email without proper owner
                if (!empty($email) && (empty($name) || empty($mobile))) {
                    $invalidOwnerRows[] = $rowNo1;
                }
            }

            if (empty($buildingName) || empty($apartmentNumber) || empty($certificateNo)) {
                $invalidRows[] = $rowNumber;
            } else {
                $validRows[] = $data;
            }

            $rowNumber++;
        }

        $originalFlats = count($validRows);
        if ($originalFlats !== $remainingFlats) {
            $this->dispatch('show-error', message: "File must contain exactly {$remainingFlats} valid flat entries to complete this society ({$existingCount} already uploaded). Found {$originalFlats}. Row(s) skipped: " . implode(', ', $invalidRows));
            return;
        }

        if (!empty($duplicateFlatErrors)) {
            $this->dispatch('show-error', message: implode(' | ', array_unique($duplicateFlatErrors)));
            return;
        }

        if ($society->is_list_of_signed_member_available == 'Yes' && !empty($missingSignedRows)) {
            log::info("Signed member data is required but missing in rows: " . implode(', ', $missingSignedRows) . ". Please fix the Excel and try again.");
            $this->dispatch('show-error', message: "Signed member data such as 'Did you purchase the apartment before the society was registered?', 'Did you sign at the time of the society registration?', 'Did the previous owner sign the registration documents?', 'Has the flat transfer-related fee been paid to the Society?', or 'Have physical documents been submitted to the society?' must be provided in rows: " . implode(', ', $missingSignedRows));
            return;
        }

        if ($society->is_list_of_signed_member_available == 'Yes' && !empty($missingOwnerRows)) {
            log::info("Signed member data is required but missing in rows: " . implode(', ', $missingOwnerRows) . ". Please fix the Excel and try again.");
            $this->dispatch('show-error', message: "Is Membership Application Signed is YES, so owner details (name and mobile number) must be provided for rows: " . implode(', ', $missingOwnerRows));
            return;
        }


        if ($society->is_list_of_signed_member_available == 'No' && $hasUnwantedSignedMemberData) {
            log::info("Unwanted signed member data found in Excel, but it will be rejected as per society settings. Data will not be imported. Please fix the Excel and try again.");
            $this->dispatch('show-error', message: "Signed member list is selected as No. It must remain empty. Please do not provide Signed member data such as 'Did you purchase the apartment before the society was registered?', 'Did you sign at the time of the society registration?', 'Did the previous owner sign the registration documents?', 'Has the flat transfer-related fee been paid to the Society?', or 'Have physical documents been submitted to the society?'.");
            return;
        }

        // Check for invalid signed member values
        if (!empty($invalidSignedValueRows)) {
            $errorMessages = [];
            foreach ($invalidSignedValueRows as $rowNum => $messages) {
                foreach ($messages as $msg) {
                    $errorMessages[] = "Row {$rowNum}: {$msg}";
                }
            }
            $this->dispatch('show-error', message: implode(' | ', $errorMessages));
            return;
        }

        if (!empty($invalidOwnerRows)) {
            $this->dispatch(
                'show-error',
                message: "Owner details are invalid (name/mobile empty or mismatch) in rows: " . implode('| ', array_unique($invalidOwnerRows))
            );
            return;
        }

        $allMobiles = [];

        $existingMobiles = SocietyDetail::select('owner1_mobile', 'owner2_mobile', 'owner3_mobile')
            ->get()
            ->flatMap(function ($item) {
                return [
                    $item->owner1_mobile,
                    $item->owner2_mobile,
                    $item->owner3_mobile
                ];
            })
            ->filter()
            ->toArray();
        foreach ($validRows as $index => $data) {
            $rowNo2 = $index + 2;

            // Extract original mobile values for format validation
            $originalMobiles = [
                'owner1_mobile' => (string) ($data[$indexes['owner1_mobile']] ?? ''),
                'owner2_mobile' => (string) ($data[$indexes['owner2_mobile']] ?? ''),
                'owner3_mobile' => (string) ($data[$indexes['owner3_mobile']] ?? ''),
            ];

            // Check mobile format (must contain only digits if not empty)
            foreach ($originalMobiles as $field => $value) {
                if (empty($value)) continue;

                if (!ctype_digit($value)) {
                    $errors[] = "Row {$rowNo2}: {$field} must contain only numbers (digits), found: {$value}";
                }
            }

            // Clean mobile numbers (remove non-digits)
            $mobileFields = [
                'owner1_mobile' => preg_replace('/\D/', '', $originalMobiles['owner1_mobile']),
                'owner2_mobile' => preg_replace('/\D/', '', $originalMobiles['owner2_mobile']),
                'owner3_mobile' => preg_replace('/\D/', '', $originalMobiles['owner3_mobile']),
            ];

            log::info("Processing row {$rowNo2} with mobiles: " . implode(', ', $mobileFields));
            // 1. Check duplicate inside SAME ROW
            $filtered = array_filter($mobileFields);
            $counts = array_count_values($filtered);

            foreach ($counts as $mobile => $count) {
                log::info("Row {$rowNo2} mobile {$mobile} count: {$count}");
                if ($count > 1) {
                    foreach ($mobileFields as $field => $value) {
                        if ($value == $mobile) {
                            $errors[] = "Row {$rowNo2}: {$field} ({$mobile}) duplicated within same row";
                        }
                    }
                }
            }

            // Check mobile digit length (must be 10 digits)
            foreach ($mobileFields as $field => $mobile) {
                if (empty($mobile)) continue;

                if (strlen($mobile) !== 10) {
                    $errors[] = "Row {$rowNo2}: {$field} ({$mobile}) must be exactly 10 digits";
                }
            }

            // 2. Check duplicate in FILE
            foreach ($mobileFields as $field => $mobile) {
                if (empty($mobile)) continue;

                if (in_array($mobile, $allMobiles)) {
                    $errors[] = "Row {$rowNo2}: {$field} ({$mobile}) duplicated in file";
                } else {
                    $allMobiles[] = $mobile;
                }

                if (in_array($mobile, $existingMobiles)) {
                    $errors[] = "Row {$rowNo2}: {$field} ({$mobile}) already have saved in system for another flat";
                }
            }
        }
        # FINAL VALIDATION (AFTER LOOP)
        if (!empty($errors)) {
            $this->dispatch('show-error', message: implode(' | ', array_unique($errors)));
            return;
        }

        DB::beginTransaction();

        try {
            // timeline
            $this->timelines = Timeline::orderBy('id')->get();
            $this->timelineValues = array_values($this->timelines->pluck('name')->toArray());

            $insertedCount = 0;
            foreach ($validRows as $index => $data) {
                $owner1_name = trim($data[$indexes['owner1_first_name']] . ' ' . ($data[$indexes['owner1_middle_name']] ?? '') . ' ' . ($data[$indexes['owner1_last_name']] ?? ''));
                $owner2_name = trim(($data[$indexes['owner2_first_name']] ?? '') . ' ' . ($data[$indexes['owner2_middle_name']] ?? '') . ' ' . ($data[$indexes['owner2_last_name']] ?? ''));
                $owner3_name = trim(($data[$indexes['owner3_first_name']] ?? '') . ' ' . ($data[$indexes['owner3_middle_name']] ?? '') . ' ' . ($data[$indexes['owner3_last_name']] ?? ''));

                $status = [
                    "tasks" => [
                        [
                            "name" => $this->timelineValues[0],
                            "responsibilityOf" => "ApartmentOwner",
                            "Status" => "Pending",
                            "createdBy" => "System",
                            "createDateTime" => now(),
                            "updatedBy" => null,
                            "updateDateTime" => null
                        ],
                        [
                            "name" => $this->timelineValues[1],
                            "responsibilityOf" => "ApartmentOwner",
                            "Status" => "Pending",
                            "createdBy" => null,
                            "createDateTime" => now(),
                            "updatedBy" => null,
                            "updateDateTime" => null,
                        ],
                        [
                            "name" => $this->timelineValues[2],
                            "responsibilityOf" => "DearSociety",
                            "Status" => "Pending",
                            "createdBy" => "System",
                            "createDateTime" => null,
                            "updatedBy" => null,
                            "updateDateTime" => null
                        ],
                        [
                            "name" => $this->timelineValues[3],
                            "responsibilityOf" => "DearSociety",
                            "Status" => "Pending",
                            "createdBy" => null,
                            "createDateTime" => null,
                            "updatedBy" => null,
                            "updateDateTime" => null
                        ],
                        [
                            "name" => $this->timelineValues[4],
                            "responsibilityOf" => "DearSociety",
                            "Status" => "Pending",
                            "createdBy" => null,
                            "createDateTime" => null,
                            "updatedBy" => null,
                            "updateDateTime" => null
                        ]
                    ]
                ];
                $owner1MobileVal = $data[$indexes['owner1_mobile']] ?? null;
                $owner2MobileVal = $data[$indexes['owner2_mobile']] ?? null;
                $owner3MobileVal = $data[$indexes['owner3_mobile']] ?? null;

                if (!empty(trim((string)$owner1MobileVal)) || !empty(trim((string)$owner2MobileVal)) || !empty(trim((string)$owner3MobileVal))) {
                    $aptNo = $data[$indexes['apartment_number']] ?? '';
                    $status['password'] = $society->registration_no . '_' . $aptNo;
                }

                SocietyDetail::updateOrCreate(
                    [
                        'society_id' => $this->selectedSocietyId,
                        'building_name' => $data[$indexes['building_name']],
                        'apartment_number' => $data[$indexes['apartment_number']],
                    ],
                    [
                        'user_id' => Auth::id(),
                        'certificate_no' => $data[$indexes['certificate_no']],
                        'did_you_purchase_the_apartment_before_the_society_was_registered' => $idxSigned1 !== null ? ($data[$idxSigned1] ?? 'No') : 'No',
                        'did_you_sign_at_the_time_of_the_society_registration' => $idxSigned2 !== null ? ($data[$idxSigned2] ?? 'No') : 'No',
                        'did_the_previous_owner_sign_the_registration_documents' => $idxSigned3 !== null ? ($data[$idxSigned3] ?? 'No') : 'No',
                        'has_the_flat_transfer_related_fee_been_paid_to_the_society' => $idxSigned4 !== null ? ($data[$idxSigned4] ?? null) : null,
                        'have_physical_documents_been_submitted_to_the_society' => $idxSigned5 !== null ? ($data[$idxSigned5] ?? 'No') : 'No',
                        'owner1_name' => $owner1_name,
                        'owner1_mobile' => $data[$indexes['owner1_mobile']] ?? null,
                        'owner1_email' => $data[$indexes['owner1_email']] ?? null,
                        'owner2_name' => $owner2_name ?: null,
                        'owner2_mobile' => $data[$indexes['owner2_mobile']] ?? null,
                        'owner2_email' => $data[$indexes['owner2_email']] ?? null,
                        'owner3_name' => $owner3_name ?: null,
                        'owner3_mobile' => $data[$indexes['owner3_mobile']] ?? null,
                        'owner3_email' => $data[$indexes['owner3_email']] ?? null,
                        'status' => json_encode($status)
                    ]
                );
                $insertedCount++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        if ($insertedCount == $originalFlats) {
            $this->dispatch('show-success', message: "{$originalFlats} entries inserted successfully!");
            $this->signedMembersFile = null;
            // Recheck the counts
            $this->updatedEditIsListOfSignedMemberAvailable($this->edit_is_list_of_signed_member_available);
            $this->refreshCounts();
        } else {
            $this->dispatch('show-error', message: "Society information could not be saved due to some error!");
        }
    }

    public function closeEditSocietyModal()
    {
        $this->reset([
            'edit_society_name',
            'edit_registration_no',
            'edit_total_building',
            'edit_total_flats',
            'edit_address_1',
            'edit_address_2',
            'edit_pincode',
            'edit_state_id',
            'edit_city_id',
            'edit_no_of_shares',
            'edit_share_value',
            'edit_is_list_of_signed_member_available',
            'edit_is_byelaws_available',
            'cities',
            'signedMembersFile',
            'signedMembersMessage',
            'showSignedMembersUploader',
        ]);
        $this->showEditSocietyModal = false;
    }

    public function saveShares()
    {
        $this->validate([
            'no_of_shares' => 'required|numeric|min:1',
            'share_value' => 'required|numeric|min:1',
        ]);

        $society = Society::find($this->selectedSocietyId);

        if (!$society) {
            $this->dispatch('show-error', message: 'Society not found.');
            return;
        }

        // Check if shares already exist
        if (empty($society->no_of_shares) && empty($society->share_value)) {
            $message = 'Shares assigned successfully!';
        } else {
            $message = 'Shares updated successfully!';
        }

        $society->update([
            'no_of_shares' => $this->no_of_shares,
            'share_value' => $this->share_value,
        ]);

        $this->dispatch('show-success', message: $message);

        $this->showAssignModal = false;
    }

    // public function updatedAssignType($value)
    // {
    //     if ($value === 'individual') {
    //         $this->apartments = SocietyDetail::where('society_id', $this->selectedSocietyId)
    //             ->get()
    //             ->map(fn($a) => [
    //                 'id' => $a->id,
    //                 'name' => $a->building_name,
    //                 'individual_no_of_share' => '',
    //                 'share_capital_amount' => '',
    //             ])
    //             ->toArray();
    //     } else {
    //         $this->apartments = [];
    //     }
    // }

    // public function saveEqualShares()
    // {
    //     $this->validate([
    //         'individual_no_of_share' => 'required|numeric|min:1',
    //         'share_capital_amount' => 'required|numeric|min:1',
    //     ]);

    //     $society = Society::find($this->selectedSocietyId);

    //     if (!$society) {
    //         $this->dispatch('show-error', message: 'Society not found.');
    //         return;
    //     }
    //     $totalApartments = SocietyDetail::where('society_id', $this->selectedSocietyId)->count();
    //     $totalAssignedShares = $this->individual_no_of_share * $totalApartments;
    //     $totalAssignedCapital = $this->share_capital_amount * $totalApartments;
    //     $expectedTotalShares = $society->no_of_shares;
    //     $expectedTotalCapital = $society->no_of_shares * $society->share_value;
    //     if ($totalAssignedShares != $expectedTotalShares) {
    //         $this->dispatch('show-error', message: "Total assigned shares ($totalAssignedShares) do not match society's total shares ($expectedTotalShares).");
    //         $this->showAssignModal = false;
    //         return;
    //     }

    //     if ($totalAssignedCapital != $expectedTotalCapital) {
    //         $this->dispatch('show-error', message: "Total share capital ($totalAssignedCapital) does not match expected capital ($expectedTotalCapital).");
    //         $this->showAssignModal = false;
    //         return;
    //     }
    //     $response = false;
    //     $response = SocietyDetail::where('society_id', $this->selectedSocietyId)->update([
    //         'no_of_shares' => $this->individual_no_of_share,
    //         'share_capital_amount' => $this->share_capital_amount,
    //     ]);

    //     if ($response !== false) {
    //         $this->dispatch('show-success', message: 'Shares assigned equally to all apartments!');
    //         $this->reset(['individual_no_of_share', 'share_capital_amount']);
    //         $this->showAssignModal = false;
    //     } else {
    //         $this->dispatch('show-error', message: 'Some error occurs while assign shares equally to all apartments!');
    //         $this->showAssignModal = false;
    //     }
    // }

    // public function saveIndividualShares()
    // {
    //     $rules = [
    //         'apartments.*.individual_no_of_share' => 'required|numeric|min:1',
    //         'apartments.*.share_capital_amount' => 'required|numeric|min:1',
    //     ];

    //     $messages = [
    //         'required' => 'The :attribute field is required.',
    //         'numeric' => 'The :attribute must be a number.',
    //         'min' => 'The :attribute must be at least :min.',
    //     ];

    //     $attributes = [];
    //     foreach ($this->apartments as $index => $apt) {
    //         $attributes["apartments.$index.individual_no_of_share"] = "Shares for {$apt['name']}";
    //         $attributes["apartments.$index.share_capital_amount"] = "Share Amount for {$apt['name']}";
    //     }

    //     $this->validate($rules, $messages, $attributes);
    //     $totalIndividualShares = collect($this->apartments)->sum('individual_no_of_share');
    //     $totalShareCapital = collect($this->apartments)->sum('share_capital_amount');
    //     $society = Society::find($this->selectedSocietyId); // or however you link it
    //     if (!$society) {
    //         $this->dispatch('show-error', message: 'Society record not found.');
    //         return;
    //     }
    //     $expectedShareCapital = $society->no_of_shares * $society->share_value;
    //     if ($totalIndividualShares != $society->no_of_shares) {
    //         $this->dispatch('show-error', message: "Total individual shares ($totalIndividualShares) do not match society's total shares ({$society->no_of_shares}).");
    //         $this->showAssignModal = false;
    //         return;
    //     }

    //     if ($totalShareCapital != $expectedShareCapital) {
    //         $this->dispatch('show-error', message: "Total share capital ({$totalShareCapital}) does not match society's expected capital ({$expectedShareCapital}).");
    //         $this->showAssignModal = false;
    //         return;
    //     }
    //     $response = false;
    //     foreach ($this->apartments as $apartment) {
    //         $response = SocietyDetail::where('id', $apartment['id'])->update([
    //             'no_of_shares' => $apartment['individual_no_of_share'],
    //             'share_capital_amount' => $apartment['share_capital_amount'],
    //         ]);
    //     }

    //     if ($response !== false) {
    //         $this->dispatch('show-success', message: 'Individual shares assigned to all apartments successfully!');
    //         $this->showAssignModal = false;
    //         $this->reset(['apartments']);
    //         $this->resetErrorBag();
    //     } else {
    //         $this->dispatch('show-error', message: 'Some error occurs while assign shares equally to all apartments!');
    //         $this->showAssignModal = false;
    //     }
    // }

    public function closeModal()
    {
        $this->reset([
            'no_of_shares',
            'share_value',
            'assignType',
            'societyDetails',
            'step',
        ]);
        $this->showAssignModal = false;
    }
}
