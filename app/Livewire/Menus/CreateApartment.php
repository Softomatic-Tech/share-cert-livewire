<?php

namespace App\Livewire\Menus;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Society;
use App\Models\SocietyDetail;
use App\Models\Timeline;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CreateApartment extends Component
{
    use WithFileUploads;
    public $csv_file, $society_id;
    public $timelines, $timelineValues, $timelineMap;
    public $society = [];

    public function render()
    {
        return view('livewire.menus.create-apartment');
    }

    public function redirectToApartmentPage()
    {
        return redirect()->route('admin.view-apartments');
    }

    public function mount()
    {
        $this->society = Society::all();
    }

    public function csvExport(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sample_society_details.csv"',
        ];

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


        $columns = [
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
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns); // write headers only
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function saveApartment()
    {
        $this->validate([
            'society_id' => 'required|exists:societies,id',
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);
        $path = $this->csv_file->store('temp');
        $fullPath = Storage::path($path);
        $file = fopen($fullPath, 'r');
        if (!$file) {
            $this->dispatch('show-error', message: 'Unable to open the uploaded CSV file.');
            return;
        }
        $header = fgetcsv($file);
        $requiredHeaders = ['building_name', 'apartment_number', 'certificate_no'];
        $headerMap = array_map('trim', $header);
        foreach ($requiredHeaders as $required) {
            if (!in_array($required, $headerMap)) {
                $this->dispatch('show-error', message: 'CSV is missing required column: {$required}');
                fclose($file);
                return;
            }
        }
        $indexes = array_flip($headerMap);
        $idxSigned1 = $indexes['is_membership_application_signed (Yes/No)'] ?? $indexes['is_membership_application_signed(Yes/No)'] ?? $indexes['is_membership_application_signed'] ?? null;
        $idxSigned2 = $indexes['is_membership_application_signed_by_one_of_the_current_owners (Yes/No)'] ?? $indexes['is_membership_application_signed_by_one_of_the_current_owners(Yes/No)'] ?? $indexes['is_membership_application_signed_by_one_of_the_current_owners'] ?? null;
        $idxSigned3 = $indexes['signed_member_name'] ?? null;
        $insertedCount = 0;
        $validRows = [];
        $invalidRows = [];
        $rowNumber = 2;
        $shareMismatchError = '';
        $uploadedShares = 0;
        // $uploadedAmount = 0;
        $society = Society::find($this->society_id);
        $expectedFlats = (int) $society->total_flats;
        $hasSignedMemberData = false;
        $hasUnwantedSignedMemberData = false;

        // $expectedShares = (float) $society->no_of_shares;
        // $expectedAmount = (float) ($society->no_of_shares * $society->share_value ?? 0);
        while (($data = fgetcsv($file)) !== FALSE) {
            $buildingName = $data[$indexes['building_name']] ?? null;
            $apartmentNumber = $data[$indexes['apartment_number']] ?? null;
            $certificateNo = $data[$indexes['certificate_no']] ?? null;
            // $noOfShares = $data[$indexes['individual_no_of_share']] ?? null;
            // $shareCapitalAmount = $data[$indexes['share_capital_amount']] ?? null;
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
                // if (!is_numeric($noOfShares)) {
                //     $invalidRows[] = $rowNumber;
                // } else {
                // $uploadedShares += (float) $noOfShares;
                // $uploadedAmount += (float) $shareCapitalAmount;
                $validRows[] = $data;
                //}
            }
            $rowNumber++;
        }
        fclose($file);

        if ($society->is_list_of_signed_member_available == 'Yes' && !$hasSignedMemberData) {
            $this->dispatch('show-error', message: "Signed member list is selected as Yes. The CSV must have at least one value for 'is membership application signed', 'is membership application signed by one of the current owners', or 'signed member name'.");
            return;
        }

        if ($society->is_list_of_signed_member_available == 'No' && $hasUnwantedSignedMemberData) {
            $this->dispatch('show-error', message: "Signed member list is selected as No. It must remain empty. Please do not provide 'is membership application signed', 'is membership application signed by one of the current owners', or 'signed member name'.");
            return;
        }

        $csvFlats = count($validRows);
        if ($csvFlats !== $expectedFlats) {
            $this->dispatch('show-error', message: "CSV must contain exactly {$expectedFlats} valid flat entries. Found {$csvFlats}. Row(s) skipped: " . implode(', ', $invalidRows));
            return;
        }
        // if ($uploadedShares != $expectedShares) {
        //     $diffOfShare = (float)$uploadedShares - (float)$expectedShares;
        //         if($uploadedAmount != $expectedAmount){
        //             $diffOfAmount = (float)$uploadedAmount - (float)$expectedAmount;
        //             $status1 = $diffOfShare > 0 ? 'more' : 'less';
        //             $status2 = $diffOfAmount > 0 ? 'more' : 'less';
        //             $shareMismatchError = "Total shares and amount mismatch! Expected shares {$expectedShares}, but found {$uploadedShares} ({$status1} by " . abs($diffOfShare) .") and expected amount {$expectedAmount}, but found {$uploadedAmount} ({$status2} by " . abs($diffOfAmount). ") in CSV.";
        //             $this->dispatch('show-error', message:  $shareMismatchError);
        //             return;
        //         }else{
        //             $status1 = $diffOfShare > 0 ? 'more' : 'less';
        //             $shareMismatchError = "Total shares mismatch! Expected shares {$expectedShares}, but found {$uploadedShares} in CSV ({$status1} by " . abs($diffOfShare) . ").";
        //             $this->dispatch('show-error', message:  $shareMismatchError);
        //             return;
        //         }
        // }
        // else{
        //         if($uploadedAmount != $expectedAmount){
        //             $diffOfAmount = (float)$uploadedAmount - (float)$expectedAmount;
        //             $status2 = $diffOfAmount > 0 ? 'more' : 'less';
        //             $shareMismatchError = "Total shares amount mismatch! Expected amount {$expectedAmount}, but found {$uploadedAmount} ({$status2} by " . abs($diffOfAmount). ") in CSV.";
        //             $this->dispatch('show-error', message:  $shareMismatchError);
        //             return;
        //         }
        // }

        $this->timelines = Timeline::orderBy('id')->get();
        $this->timelineMap = $this->timelines->pluck('name', 'id')->toArray();
        $this->timelineValues = array_values($this->timelineMap);
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
                    'society_id' => $this->society_id,
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

        if ($insertedCount == $csvFlats) {
            $this->dispatch('show-success', message: "{$csvFlats} entries inserted successfully!");
            $this->reset('csv_file', 'society_id');
        } else {
            $this->dispatch('show-error', message: "Society information could not be saved due to some error!");
        }
    }
}
