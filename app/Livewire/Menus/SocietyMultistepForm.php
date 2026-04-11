<?php

namespace App\Livewire\Menus;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Society;
use App\Models\SocietyDetail;
use App\Models\State;
use App\Models\City;
use App\Models\User;
use App\Models\Timeline;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ApartmentImport;
use App\Exports\ApartmentExport;

class SocietyMultistepForm extends Component
{
    use WithFileUploads;
    public $currentStep = 1;
    public $excel_file;
    public $societyId = null;
    public $fileUploaded = false;
    public $timelines, $timelineValues, $timelineMap;
    public $states, $cities = [], $admins = [];
    public $formData = [
        'society_name' => '',
        'total_building' => '',
        'total_flats' => '',
        'address_1' => '',
        'address_2' => '',
        'pincode' => '',
        'state_id' => 32,
        'city_id' => '',
        'registration_no' => '',
        'no_of_shares' => '',
        'share_value' => '',
        // 'i_register' => '',
        // 'j_register' => '',
        'admin_id' => '',
        'is_list_of_signed_member_available' => 'No',
        'is_byelaws_available' => 'No',
    ];

    // Validation rules for each step
    protected $rules = [
        1 => [
            'formData.society_name' => 'required|string|max:255',
            'formData.total_building' => 'required|numeric',
            'formData.total_flats' => 'required|numeric|min:1',
            'formData.address_1' => 'required|string|max:1000',
            'formData.address_2' => 'nullable|string|max:255',
            'formData.pincode' => 'required|numeric|digits:6',
            'formData.state_id' => 'required|exists:states,id',
            'formData.city_id' => 'required|exists:cities,id',
            'formData.registration_no' => 'required|string|max:255',
            'formData.no_of_shares' => 'required|numeric|min:1',
            'formData.share_value' => 'required|numeric|decimal:0,2',
            // 'formData.i_register' => 'nullable|string|max:255',
            'formData.j_register' => 'nullable|string|max:255',
            'formData.admin_id' => 'required|exists:users,id',
            'formData.is_list_of_signed_member_available' => 'required|in:Yes,No',
            'formData.is_byelaws_available' => 'required|in:Yes,No',
        ],
        2 => [],
        3 => []
    ];

    protected $validationAttributes = [
        'formData.society_name' => 'society name',
        'formData.address_1'    => 'address line 1',
        'formData.address_2'    => 'address line 2',
        'formData.pincode'      => 'pincode',
        'formData.state_id'     => 'state',
        'formData.city_id'      => 'city',
        'formData.total_flats'  => 'total flats',
        'formData.registration_no'  => 'registration no',
        'formData.no_of_shares'  => 'no of shares',
        'formData.share_value'  => 'share value',
        // 'formData.i_register'   => 'I register',
        // 'formData.j_register'   => 'J register',
        'formData.admin_id'     => 'assigned admin',
    ];
    public function render()
    {
        return view('livewire.menus.society-multistep-form');
    }

    public function mount()
    {
        $this->states = State::orderBy('name', 'asc')->get();
        // Set default state to Maharashtra (ID 32)
        $this->formData['state_id'] = 32;
        $this->cities = City::where('state_id', 32)->get();

        $this->admins = User::whereHas('role', function ($query) {
            $query->where('role', 'Admin');
        })->get();
    }

    public function updatedFormDataStateID($stateId)
    {
        $this->cities = City::where('state_id', $stateId)->get();
        $this->formData['city_id'] = '';
    }

    public function getUploadedDetailsProperty()
    {
        if ($this->societyId) {
            return SocietyDetail::where('society_id', $this->societyId)->get();
        }
        return collect();
    }

    public function getSocietyDetailsProperty()
    {
        return $this->societyId ? Society::with(['state', 'city'])->find($this->societyId) : null;
    }

    public function nextStep()
    {
        if ($this->currentStep == 1) {
            $this->save(); // Save Step 1 before moving
            if (!$this->societyId) {
                return; // Do not move if society not saved
            }
        }

        log::info($this->currentStep);
        if ($this->currentStep == 2) {
            log::info("Checking for uploaded society details for society ID:" . $this->societyId);
            log::info("Society details count: " . SocietyDetail::where('society_id', $this->societyId)->count());
            if (SocietyDetail::where('society_id', $this->societyId)->count() === 0) {
                $this->dispatch('show-error', message: "Please upload society details file before proceeding!");
                return;
            }
        }

        $this->currentStep++;
    }

    public function prevStep()
    {
        $this->currentStep--;
    }

    public function save()
    {
        if ($this->currentStep == 1) {
            // Check for existing society with the same registration number to prevent duplicates
            $existingSociety = Society::where('registration_no', $this->formData['registration_no'])->first();

            if ($existingSociety && $existingSociety->id !== $this->societyId) {
                // If it already has excel flats uploaded, it's a completed society
                if (SocietyDetail::where('society_id', $existingSociety->id)->exists()) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'formData.registration_no' => 'A completed society with this Registration Certificate No already exists.'
                    ]);
                } else {
                    // It was abandoned before File upload. Auto-resume to avoid creating duplicates.
                    $this->societyId = $existingSociety->id;
                }
            }
        }

        $this->validate($this->rules[$this->currentStep] ?? []);
        if ($this->societyId) {
            $society = Society::find($this->societyId);
            $society->update($this->formData);
        } else {
            $society = Society::create($this->formData);
            $this->societyId = $society->id;
        }
        if ($society) {
            $this->dispatch('show-success', message: "Society information saved successfully!");
            $this->currentStep = 1; // Reset to first step
        } else {
            $this->dispatch('show-error', message: "Society information could not be saved due to some error!");
        }
    }

    // public function csvExport(): StreamedResponse
    // {
    //     $headers = [
    //         'Content-Type' => 'text/csv',
    //         'Content-Disposition' => 'attachment; filename="sample_society_details.csv"',
    //     ];

    //     $society = Society::find($this->societyId);

    //     $columns = [
    //         'building_name',
    //         'apartment_number',
    //         'certificate_no',
    //     ];

    //     if ($society && $society->is_list_of_signed_member_available == 'Yes') {
    //         $columns = array_merge($columns, [
    //             'is_membership_application_signed (Yes/No)',
    //             'is_membership_application_signed_by_one_of_the_current_owners (Yes/No)',
    //             'signed_member_name',
    //         ]);
    //     }

    //     $columns = array_merge($columns, [
    //         'owner1_first_name',
    //         'owner1_middle_name',
    //         'owner1_last_name',
    //         'owner1_mobile',
    //         'owner1_email',
    //         'owner2_first_name',
    //         'owner2_middle_name',
    //         'owner2_last_name',
    //         'owner2_mobile',
    //         'owner2_email',
    //         'owner3_first_name',
    //         'owner3_middle_name',
    //         'owner3_last_name',
    //         'owner3_mobile',
    //         'owner3_email',
    //     ]);

    //     $callback = function () use ($columns) {
    //         $file = fopen('php://output', 'w');
    //         fputcsv($file, $columns); // write headers only
    //         fclose($file);
    //     };

    //     return response()->stream($callback, 200, $headers);
    // }

    // public function csvImport()
    // {
    //     $this->validate([
    //         'csv_file' => 'required|file|mimes:csv,txt'
    //     ]);
    //     $path = $this->csv_file->store('temp');
    //     $fullPath = Storage::path($path);
    //     $file = fopen($fullPath, 'r');
    //     if (!$file) {
    //         $this->dispatch('show-error', message: 'Unable to open the uploaded CSV file.');
    //         return;
    //     }
    //     $header = fgetcsv($file);
    //     $requiredHeaders = ['building_name', 'apartment_number', 'certificate_no'];
    //     $headerMap = array_map('trim', $header);
    //     foreach ($requiredHeaders as $required) {
    //         if (!in_array($required, $headerMap)) {
    //             $this->dispatch('show-success', message: "CSV is missing required column: {$required}");
    //             fclose($file);
    //             return;
    //         }
    //     }
    //     $indexes = array_flip($headerMap);
    //     $idxSigned1 = $indexes['is_membership_application_signed (Yes/No)'] ?? $indexes['is_membership_application_signed(Yes/No)'] ?? $indexes['is_membership_application_signed'] ?? null;
    //     $idxSigned2 = $indexes['is_membership_application_signed_by_one_of_the_current_owners (Yes/No)'] ?? $indexes['is_membership_application_signed_by_one_of_the_current_owners(Yes/No)'] ?? $indexes['is_membership_application_signed_by_one_of_the_current_owners'] ?? null;
    //     $idxSigned3 = $indexes['signed_member_name'] ?? null;

    //     $insertedCount = 0;
    //     $validRows = [];
    //     $invalidRows = [];
    //     $rowNumber = 2;
    //     // $totalCsvShares = 0;
    //     $society = Society::find($this->societyId);
    //     $expectedFlats = (int) $society->total_flats;

    //     $hasSignedMemberData = false;
    //     $hasUnwantedSignedMemberData = false;

    //     while (($data = fgetcsv($file)) !== FALSE) {
    //         $buildingName = $data[$indexes['building_name']] ?? null;
    //         $apartmentNumber = $data[$indexes['apartment_number']] ?? null;
    //         $certificateNo = $data[$indexes['certificate_no']] ?? null;
    //         // $noOfShares = $data[$indexes['individual_no_of_share']] ?? null;
    //         // $shareCapitalAmount = $data[$indexes['share_capital_amount']] ?? null;

    //         $signedCol1 = $idxSigned1 !== null ? ($data[$idxSigned1] ?? null) : null;
    //         $signedCol2 = $idxSigned2 !== null ? ($data[$idxSigned2] ?? null) : null;
    //         $signedCol3 = $idxSigned3 !== null ? ($data[$idxSigned3] ?? null) : null;

    //         if ($society->is_list_of_signed_member_available == 'Yes') {
    //             if (!empty(trim((string)$signedCol1)) || !empty(trim((string)$signedCol2)) || !empty(trim((string)$signedCol3))) {
    //                 $hasSignedMemberData = true;
    //             }
    //         } else {
    //             if (!empty(trim((string)$signedCol1)) || !empty(trim((string)$signedCol2)) || !empty(trim((string)$signedCol3))) {
    //                 $hasUnwantedSignedMemberData = true;
    //             }
    //         }

    //         if (empty($buildingName) || empty($apartmentNumber) || empty($certificateNo)) {
    //             $invalidRows[] = $rowNumber;
    //         } else {
    //             // if (!is_numeric($noOfShares)) {
    //             //     $invalidRows[] = $rowNumber;
    //             // } else {
    //             // $totalCsvShares += (float) $noOfShares;
    //             $validRows[] = $data;
    //             // }
    //         }
    //         $rowNumber++;
    //     }
    //     fclose($file);

    //     if ($society->is_list_of_signed_member_available == 'Yes' && !$hasSignedMemberData) {
    //         $this->dispatch('show-error', message: "Signed member list is selected as Yes. The CSV must have at least one value for 'is membership application signed', 'is membership application signed by one of the current owners', or 'signed member name'.");
    //         return;
    //     }

    //     if ($society->is_list_of_signed_member_available == 'No' && $hasUnwantedSignedMemberData) {
    //         $this->dispatch('show-error', message: "Signed member list is selected as No. It must remain empty. Please do not provide 'is membership application signed', 'is membership application signed by one of the current owners', or 'signed member name'.");
    //         return;
    //     }

    //     $csvFlats = count($validRows);
    //     if ($csvFlats !== $expectedFlats) {
    //         $this->dispatch('show-error', message: "CSV must contain exactly {$expectedFlats} valid flat entries. Found {$csvFlats}. Row(s) skipped: " . implode(', ', $invalidRows));
    //         return;
    //     }

    //     $this->timelines = Timeline::orderBy('id')->get();
    //     $this->timelineMap = $this->timelines->pluck('name', 'id')->toArray();
    //     $this->timelineValues = array_values($this->timelineMap);

    //     foreach ($validRows as $data) {
    //         $owner1_name = trim($data[$indexes['owner1_first_name']] . ' ' . ($data[$indexes['owner1_middle_name']] ?? '') . ' ' . ($data[$indexes['owner1_last_name']] ?? ''));
    //         $owner2_name = trim(($data[$indexes['owner2_first_name']] ?? '') . ' ' . ($data[$indexes['owner2_middle_name']] ?? '') . ' ' . ($data[$indexes['owner2_last_name']] ?? ''));
    //         $owner3_name = trim(($data[$indexes['owner3_first_name']] ?? '') . ' ' . ($data[$indexes['owner3_middle_name']] ?? '') . ' ' . ($data[$indexes['owner3_last_name']] ?? ''));
    //         $status = [
    //             "tasks" => [
    //                 [
    //                     "name" => $this->timelineValues[0],
    //                     "responsibilityOf" => "ApartmentOwner",
    //                     "Status" => "Pending",
    //                     "createdBy" => "System",
    //                     "createDateTime" => now(),
    //                     "updatedBy" => null,
    //                     "updateDateTime" => null
    //                 ],
    //                 [
    //                     "name" => $this->timelineValues[1],
    //                     "responsibilityOf" => "ApartmentOwner",
    //                     "Status" => "Pending",
    //                     "createdBy" => null,
    //                     "createDateTime" => now(),
    //                     "updatedBy" => null,
    //                     "updateDateTime" => null,
    //                 ],
    //                 [
    //                     "name" => $this->timelineValues[2],
    //                     "responsibilityOf" => "DearSociety",
    //                     "Status" => "Pending",
    //                     "createdBy" => "System",
    //                     "createDateTime" => null,
    //                     "updatedBy" => null,
    //                     "updateDateTime" => null
    //                 ],
    //                 [
    //                     "name" => $this->timelineValues[3],
    //                     "responsibilityOf" => "DearSociety",
    //                     "Status" => "Pending",
    //                     "createdBy" => null,
    //                     "createDateTime" => null,
    //                     "updatedBy" => null,
    //                     "updateDateTime" => null
    //                 ],
    //                 [
    //                     "name" => $this->timelineValues[4],
    //                     "responsibilityOf" => "DearSociety",
    //                     "Status" => "Pending",
    //                     "createdBy" => null,
    //                     "createDateTime" => null,
    //                     "updatedBy" => null,
    //                     "updateDateTime" => null
    //                 ]
    //             ]
    //         ];

    //         $owner1MobileVal = $data[$indexes['owner1_mobile']] ?? null;
    //         $owner2MobileVal = $data[$indexes['owner2_mobile']] ?? null;
    //         $owner3MobileVal = $data[$indexes['owner3_mobile']] ?? null;

    //         if (!empty(trim((string)$owner1MobileVal)) || !empty(trim((string)$owner2MobileVal)) || !empty(trim((string)$owner3MobileVal))) {
    //             $aptNo = $data[$indexes['apartment_number']] ?? '';
    //             $status['password'] = $society->registration_no . '_' . $aptNo;
    //         }

    //         SocietyDetail::updateOrCreate(
    //             [
    //                 'society_id' => $this->societyId,
    //                 'building_name' => $data[$indexes['building_name']],
    //                 'apartment_number' => $data[$indexes['apartment_number']],
    //             ],
    //             [
    //                 'user_id' => Auth::id(),
    //                 'certificate_no' => $data[$indexes['certificate_no']],
    //                 // 'no_of_shares' => $data[$indexes['individual_no_of_share']],
    //                 // 'share_capital_amount' => $data[$indexes['share_capital_amount']],
    //                 'is_membership_application_signed' => $idxSigned1 !== null ? ($data[$idxSigned1] ?? 'No') : 'No',
    //                 'is_membership_application_signed_by_one_of_the_current_owners' => $idxSigned2 !== null ? ($data[$idxSigned2] ?? 'No') : 'No',
    //                 'signed_member_name' => $idxSigned3 !== null ? ($data[$idxSigned3] ?? null) : null,
    //                 'owner1_name' => $owner1_name,
    //                 'owner1_mobile' => $data[$indexes['owner1_mobile']] ?? null,
    //                 'owner1_email' => $data[$indexes['owner1_email']] ?? null,
    //                 'owner2_name' => $owner2_name ?: null,
    //                 'owner2_mobile' => $data[$indexes['owner2_mobile']] ?? null,
    //                 'owner2_email' => $data[$indexes['owner2_email']] ?? null,
    //                 'owner3_name' => $owner3_name ?: null,
    //                 'owner3_mobile' => $data[$indexes['owner3_mobile']] ?? null,
    //                 'owner3_email' => $data[$indexes['owner3_email']] ?? null,
    //                 'status' => json_encode($status),
    //             ]
    //         );
    //         $insertedCount++;
    //     }

    //     if ($insertedCount == $csvFlats) {
    //         $this->dispatch('show-success', message: "{$csvFlats} entries inserted successfully!");
    //         $this->reset('csv_file');
    //     } else {
    //         $this->dispatch('show-error', message: "Society information could not be saved due to some error!");
    //     }
    // }

    public function excelExport()
    {
        $society = Society::find($this->societyId);

        $columns = [
            'building_name',
            'apartment_number',
            'certificate_no',
        ];

        if ($society && $society->is_list_of_signed_member_available == 'Yes') {
            $columns = array_merge($columns, [
                'is_membership_application_signed (Yes/No)',
                'is_membership_application_signed_by_one_of_the_current_owners (Yes/No)',
                'signed_member_name',
            ]);
        }

        $columns = array_merge($columns, [
            'owner1_first_name',
            'owner1_middle_name',
            'owner1_last_name',
            'owner1_mobile',
            'owner1_email',
            'owner2_first_name',
            'owner2_middle_name',
            'owner2_last_name',
            'owner2_mobile',
            'owner2_email',
            'owner3_first_name',
            'owner3_middle_name',
            'owner3_last_name',
            'owner3_mobile',
            'owner3_email',
        ]);

        return Excel::download(new ApartmentExport($columns), 'sample_society_details.xlsx');
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

        $idxSigned1 = $indexes['is_membership_application_signed (Yes/No)'] ?? $indexes['is_membership_application_signed'] ?? null;
        $idxSigned2 = $indexes['is_membership_application_signed_by_one_of_the_current_owners (Yes/No)'] ?? null;
        $idxSigned3 = $indexes['signed_member_name'] ?? null;

        $validRows = [];
        $invalidRows = [];
        $rowNumber = 2;

        $society = Society::find($this->societyId);
        $expectedFlats = (int) $society->total_flats;

        $hasSignedMemberData = false;
        $hasUnwantedSignedMemberData = false;

        foreach ($rows as $data) {

            $buildingName = $data[$indexes['building_name']] ?? null;
            $apartmentNumber = $data[$indexes['apartment_number']] ?? null;
            $certificateNo = $data[$indexes['certificate_no']] ?? null;

            $signedCol1 = $idxSigned1 !== null ? ($data[$idxSigned1] ?? null) : null;
            $signedCol2 = $idxSigned2 !== null ? ($data[$idxSigned2] ?? null) : null;
            $signedCol3 = $idxSigned3 !== null ? ($data[$idxSigned3] ?? null) : null;

            if ($society->is_list_of_signed_member_available == 'Yes') {
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
                $validRows[] = $data;
            }

            $rowNumber++;
        }

        // validations
        if ($society->is_list_of_signed_member_available == 'Yes' && !$hasSignedMemberData) {
            $this->dispatch('show-error', message: "Signed member list is selected as Yes. The CSV must have at least one value for 'is membership application signed', 'is membership application signed by one of the current owners', or 'signed member name'.");
            return;
        }

        if ($society->is_list_of_signed_member_available == 'No' && $hasUnwantedSignedMemberData) {
            $this->dispatch('show-error', message: "Signed member list is selected as No. It must remain empty. Please do not provide 'is membership application signed', 'is membership application signed by one of the current owners', or 'signed member name'.");
            return;
        }

        $originalFlats = count($validRows);
        if ($originalFlats !== $expectedFlats) {
            $this->dispatch('show-error', message: "File must contain exactly {$expectedFlats} valid flat entries. Found {$originalFlats}. Row(s) skipped: " . implode(', ', $invalidRows));
            return;
        }
        // timeline
        $this->timelines = Timeline::orderBy('id')->get();
        $this->timelineValues = array_values($this->timelines->pluck('name')->toArray());

        $insertedCount = 0;

        foreach ($validRows as $data) {

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
                    'society_id' => $this->societyId,
                    'building_name' => $data[$indexes['building_name']],
                    'apartment_number' => $data[$indexes['apartment_number']],
                ],
                [
                    'user_id' => Auth::id(),
                    'certificate_no' => $data[$indexes['certificate_no']],
                    // 'no_of_shares' => $data[$indexes['individual_no_of_share']],
                    // 'share_capital_amount' => $data[$indexes['share_capital_amount']],
                    'is_membership_application_signed' => $idxSigned1 !== null ? ($data[$idxSigned1] ?? 'No') : 'No',
                    'is_membership_application_signed_by_one_of_the_current_owners' => $idxSigned2 !== null ? ($data[$idxSigned2] ?? 'No') : 'No',
                    'signed_member_name' => $idxSigned3 !== null ? ($data[$idxSigned3] ?? null) : null,
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

        if ($insertedCount == $originalFlats) {
            $this->dispatch('show-success', message: "{$originalFlats} entries inserted successfully!");
            $this->reset('excel_file');
        } else {
            $this->dispatch('show-error', message: "Society information could not be saved due to some error!");
        }
    }

    public function excelImport()
    {
        log::info('Excel upload processing started');
        $this->validate([
            'societyId' => 'required|exists:societies,id',
            'excel_file' => 'required|file|mimes:xlsx,xls'
        ]);

        log::info('Validation passed for Excel file');
        $rows = Excel::toArray(new ApartmentImport, $this->excel_file);

        log::info('Excel file read into array');
        $data = $rows[0]; // first sheet

        if (empty($data)) {
            $this->dispatch('show-error', message: "Empty Excel file");
            return;
        }

        $header = array_map('trim', $data[0]);
        log::info('Excel header extracted', ['header' => $header]);

        unset($data[0]); // remove header
        $this->processData($header, array_values($data));
    }

    public function done()
    {
        // // Optional: Clear previous form state
        $this->reset([
            'formData',
            'excel_file',
            'societyId',
            'currentStep',
        ]);

        // Reset formData with default values
        $this->formData = [
            'society_name' => '',
            'total_building' => '',
            'total_flats' => '',
            'address_1' => '',
            'address_2' => '',
            'pincode' => '',
            'state_id' => '',
            'city_id' => '',
            'registration_no' => '',
            'no_of_shares' => '',
            'share_value' => '',
            'admin_id' => '',
            'is_list_of_signed_member_available' => 'No',
            'is_byelaws_available' => 'No',
        ];

        return redirect()->route('superadmin.dashboard');
    }
}
