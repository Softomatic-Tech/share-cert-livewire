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
            $countSigned = SocietyDetail::where('society_id', $this->selectedSocietyId)
                ->whereNotNull('is_membership_application_signed')
                ->where('is_membership_application_signed', '!=', '')
                ->count();

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

    public function uploadSignedMembers()
    {
        $this->validate([
            'signedMembersFile' => 'required|file|mimes:xlsx,xls',
        ]);

        try {
            $rows = Excel::toArray([], $this->signedMembersFile);
            $data = $rows[0]; // first sheet

            if (empty($data)) {
                $this->dispatch('show-error', message: 'Empty Excel file');
                return;
            }

            $header = array_map('trim', $data[0]);
            $requiredHeaders = ['building_name', 'apartment_number', 'certificate_no'];
            $headerMap = array_map('trim', $header);

            foreach ($requiredHeaders as $required) {
                if (!in_array($required, $headerMap)) {
                    $this->dispatch('show-error', message: "Missing required column: {$required}");
                    return;
                }
            }

            $indexes = array_flip($headerMap);

            $idxSigned1 = $indexes['is_membership_application_signed (Yes/No)'] ?? $indexes['is_membership_application_signed'] ?? null;
            $idxSigned2 = $indexes['is_membership_application_signed_by_one_of_the_current_owners (Yes/No)'] ?? $indexes['is_membership_application_signed_by_one_of_the_current_owners'] ?? null;
            $idxSigned3 = $indexes['signed_member_name'] ?? null;

            unset($data[0]); // remove header
            $validRows = [];
            $invalidRows = [];
            $rowNumber = 2;

            $society = Society::find($this->selectedSocietyId);

            // Determine if signed member list is required (current setting OR new selection)
            $signedMembersRequired = ($this->societyById->is_list_of_signed_member_available == 'Yes') || ($this->edit_is_list_of_signed_member_available == 'Yes');
            $signedMembersNotAllowed = ($this->societyById->is_list_of_signed_member_available == 'No') && ($this->edit_is_list_of_signed_member_available == 'No');

            $hasSignedMemberData = false;
            $hasUnwantedSignedMemberData = false;

            foreach ($data as $row) {
                $buildingName = $row[$indexes['building_name']] ?? null;
                $apartmentNumber = $row[$indexes['apartment_number']] ?? null;
                $certificateNo = $row[$indexes['certificate_no']] ?? null;

                // Check signed member data
                $signedCol1 = $idxSigned1 !== null ? ($row[$idxSigned1] ?? null) : null;
                $signedCol2 = $idxSigned2 !== null ? ($row[$idxSigned2] ?? null) : null;
                $signedCol3 = $idxSigned3 !== null ? ($row[$idxSigned3] ?? null) : null;

                if ($signedMembersRequired) {
                    if (!empty(trim((string)$signedCol1)) || !empty(trim((string)$signedCol2)) || !empty(trim((string)$signedCol3))) {
                        $hasSignedMemberData = true;
                    }
                } else {
                    if (!empty(trim((string)$signedCol1)) || !empty(trim((string)$signedCol2)) || !empty(trim((string)$signedCol3))) {
                        $hasUnwantedSignedMemberData = true;
                    }
                }

                if (empty($buildingName) || empty($apartmentNumber) || empty($certificateNo)) {
                    $invalidRows[] = $rowNumber;
                } else {
                    $validRows[] = $row;
                }
                $rowNumber++;
            }

            // Validation: Check signed member data requirements
            if ($signedMembersRequired && !$hasSignedMemberData) {
                $this->dispatch('show-error', message: "Signed member list is selected as Yes. The Excel file must have at least one value for 'is membership application signed', 'is membership application signed by one of the current owners', or 'signed_member_name' in at least one row.");
                return;
            }

            if ($signedMembersNotAllowed && $hasUnwantedSignedMemberData) {
                $this->dispatch('show-error', message: "Signed member list is selected as No. The Excel file must not contain any values for 'is membership application signed', 'is membership application signed by one of the current owners', or 'signed_member_name'.");
                return;
            }

            if (!empty($invalidRows)) {
                $this->dispatch('show-error', message: 'Invalid rows: ' . implode(', ', $invalidRows));
                return;
            }

            DB::beginTransaction();

            try {

                $inserted = 0;
                $matched = 0;
                // timeline
                $this->timelines = Timeline::orderBy('id')->get();
                $this->timelineValues = array_values($this->timelines->pluck('name')->toArray());
                foreach ($validRows as $row) {
                    $building = trim($row[$indexes['building_name']] ?? '');
                    $apt = trim($row[$indexes['apartment_number']] ?? '');
                    $cert = trim($row[$indexes['certificate_no']] ?? '');

                    // Extract owner details
                    $owner1_name = trim(
                        ($row[$indexes['owner1_first_name']] ?? '') . ' ' .
                            ($row[$indexes['owner1_middle_name']] ?? '') . ' ' .
                            ($row[$indexes['owner1_last_name']] ?? '')
                    );
                    $owner2_name = trim(
                        ($row[$indexes['owner2_first_name']] ?? '') . ' ' .
                            ($row[$indexes['owner2_middle_name']] ?? '') . ' ' .
                            ($row[$indexes['owner2_last_name']] ?? '')
                    );
                    $owner3_name = trim(
                        ($row[$indexes['owner3_first_name']] ?? '') . ' ' .
                            ($row[$indexes['owner3_middle_name']] ?? '') . ' ' .
                            ($row[$indexes['owner3_last_name']] ?? '')
                    );


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

                    $owner1MobileVal = $row[$indexes['owner1_mobile']] ?? null;
                    $owner2MobileVal = $row[$indexes['owner2_mobile']] ?? null;
                    $owner3MobileVal = $row[$indexes['owner3_mobile']] ?? null;

                    if (!empty(trim((string)$owner1MobileVal)) || !empty(trim((string)$owner2MobileVal)) || !empty(trim((string)$owner3MobileVal))) {
                        $aptNo = $row[$indexes['apartment_number']] ?? '';
                        $status['password'] = $society->registration_no . '_' . $aptNo;
                    }


                    $existing = SocietyDetail::where('society_id', $this->selectedSocietyId)
                        ->where('building_name', $building)
                        ->where('apartment_number', $apt)
                        ->where('certificate_no', $cert)
                        ->first();

                    if ($existing) {
                        $matched++;
                    }

                    SocietyDetail::updateOrCreate(
                        [
                            'society_id' => $this->selectedSocietyId,
                            'building_name' => $building,
                            'apartment_number' => $apt,
                            'certificate_no' => $cert,
                        ],
                        [
                            'user_id' => Auth::id(),
                            'is_membership_application_signed' => $idxSigned1 !== null ? (trim($row[$idxSigned1] ?? 'No')) : 'No',
                            'is_membership_application_signed_by_one_of_the_current_owners' => $idxSigned2 !== null ? (trim($row[$idxSigned2] ?? 'No')) : 'No',
                            'signed_member_name' => $idxSigned3 !== null ? trim($row[$idxSigned3] ?? '') : null,
                            'owner1_name' => !empty($owner1_name) ? $owner1_name : null,
                            'owner1_mobile' => $row[$indexes['owner1_mobile']] ?? null,
                            'owner1_email' => $row[$indexes['owner1_email']] ?? null,
                            'owner2_name' => !empty($owner2_name) ? $owner2_name : null,
                            'owner2_mobile' => $row[$indexes['owner2_mobile']] ?? null,
                            'owner2_email' => $row[$indexes['owner2_email']] ?? null,
                            'owner3_name' => !empty($owner3_name) ? $owner3_name : null,
                            'owner3_mobile' => $row[$indexes['owner3_mobile']] ?? null,
                            'owner3_email' => $row[$indexes['owner3_email']] ?? null,
                            'status' => json_encode($status)
                        ]
                    );

                    $inserted++;
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            $message = "Inserted {$inserted} signed member entries.";
            if ($matched > 0) {
                $message .= " {$matched} entries matched existing records but were inserted as new entries.";
            }

            $this->dispatch('show-success', message: $message);
            $this->signedMembersFile = null;
            // Recheck the counts
            $this->updatedEditIsListOfSignedMemberAvailable($this->edit_is_list_of_signed_member_available);
            $this->refreshCounts();
        } catch (\Exception $e) {
            $this->dispatch('show-error', message: 'Error uploading file: ' . $e->getMessage());
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
