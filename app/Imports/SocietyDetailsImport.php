<?php

namespace App\Imports;

use App\Models\Society;
use App\Models\SocietyDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SocietyDetailsImport implements ToCollection, WithHeadingRow
{
    protected $societyId;

    public function __construct($societyId)
    {
        $this->societyId = $societyId;
    }

    public function collection(Collection $rows)
    {
        $society = Society::find($this->societyId);
        $expectedFlats = (int) $society->total_flats;

        if ($rows->count() !== $expectedFlats) {
            throw new \Exception("Excel must contain exactly {$expectedFlats} valid flat entries. Found {$rows->count()}.");
        }

        $insertedCount = 0;
        $validRows = [];
        $invalidRows = [];
        $rowNumber = 2;

        foreach ($rows as $index => $row) {
            $buildingName = $row['building_name'] ?? null;
            $apartmentNumber = $row['apartment_number'] ?? null;
            $certificateNo = $row['certificate_no'] ?? null;

            $signedCol1 = $row['is_membership_application_signed_yes_no'] ?? $row['is_membership_application_signed'] ?? null;
            $signedCol2 = $row['is_membership_application_signed_by_one_of_the_current_owners_yes_no'] ?? $row['is_membership_application_signed_by_one_of_the_current_owners'] ?? null;
            $signedCol3 = $row['signed_member_name'] ?? null;

            if (empty($buildingName) || empty($apartmentNumber) || empty($certificateNo)) {
                $invalidRows[] = $rowNumber;
            } else {
                $validRows[] = [
                    'building_name' => $buildingName,
                    'apartment_number' => $apartmentNumber,
                    'certificate_no' => $certificateNo,
                    'owner1_first_name' => $row['owner1_first_name'] ?? null,
                    'owner1_middle_name' => $row['owner1_middle_name'] ?? null,
                    'owner1_last_name' => $row['owner1_last_name'] ?? null,
                    'owner1_mobile' => $row['owner1_mobile'] ?? null,
                    'owner1_email' => $row['owner1_email'] ?? null,
                    'owner2_first_name' => $row['owner2_first_name'] ?? null,
                    'owner2_middle_name' => $row['owner2_middle_name'] ?? null,
                    'owner2_last_name' => $row['owner2_last_name'] ?? null,
                    'owner2_mobile' => $row['owner2_mobile'] ?? null,
                    'owner2_email' => $row['owner2_email'] ?? null,
                    'owner3_first_name' => $row['owner3_first_name'] ?? null,
                    'owner3_middle_name' => $row['owner3_middle_name'] ?? null,
                    'owner3_last_name' => $row['owner3_last_name'] ?? null,
                    'owner3_mobile' => $row['owner3_mobile'] ?? null,
                    'owner3_email' => $row['owner3_email'] ?? null,
                ];
            }
            $rowNumber++;
        }

        if (!empty($invalidRows)) {
            throw new \Exception("Invalid rows: " . implode(', ', $invalidRows));
        }

        foreach ($validRows as $data) {
            SocietyDetail::create(array_merge($data, ['society_id' => $this->societyId]));
        }
    }
}
