<?php

namespace App\Services;

use App\Models\Society;
use App\Models\SocietyDetail;
use App\Models\Timeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExcelImportService
{
    public function processData($header, $rows, $societyId)
    {
        $requiredHeaders = ['building_name', 'apartment_number', 'certificate_no'];
        $headerMap = array_map('trim', $header);

        foreach ($requiredHeaders as $required) {
            if (!in_array($required, $headerMap)) {
                return [
                    'status' => 'error',
                    'message' => "Missing column: {$required}"
                ];
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

        $society = Society::find($societyId);
        $expectedFlats = (int) $society->total_flats;
        $existingCount = SocietyDetail::where('society_id', $societyId)->count();
        $remainingFlats = $expectedFlats - $existingCount;

        log::info('expectedFlats: ' . $expectedFlats);
        log::info('existingCount: ' . $existingCount);
        log::info('remainingFlats: ' . $remainingFlats);
        if ($remainingFlats <= 0) {
            return [
                'status' => 'error',
                'message' => "All {$expectedFlats} flats are already uploaded for this society. Import not allowed."
            ];
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
        if ($originalFlats > $remainingFlats || $originalFlats == 0) {
            return [
                'status' => 'error',
                'message' => "File must contain at most {$remainingFlats} valid flat entries (remaining to upload). Found {$originalFlats}. Row(s) skipped: " . implode(', ', $invalidRows)
            ];
        }

        if (!empty($duplicateFlatErrors)) {
            return [
                'status' => 'error',
                'message' => implode(' | ', array_unique($duplicateFlatErrors))
            ];
        }

        log::info('if is_list_of_signed_member_available is yes , missingSignedRows');
        log::info($missingSignedRows);

        if ($society->is_list_of_signed_member_available == 'Yes' && !empty($missingSignedRows)) {
            return [
                'status' => 'error',
                'message' => "Signed member data such as 'Did you purchase the apartment before the society was registered?', 'Did you sign at the time of the society registration?', 'Did the previous owner sign the registration documents?', 'Has the flat transfer-related fee been paid to the Society?', or 'Have physical documents been submitted to the society?' must be provided in rows: " . implode(', ', $missingSignedRows)
            ];
        }

        if ($society->is_list_of_signed_member_available == 'Yes' && !empty($missingOwnerRows)) {
            log::info("Signed member data is required but missing in rows: " . implode(', ', $missingOwnerRows) . ". Please fix the Excel and try again.");
            return [
                'status' => 'error',
                'message' => "Is Membership Application Signed is YES, so owner details (name and mobile number) must be provided for rows: " . implode(', ', $missingOwnerRows)
            ];
        }


        if ($society->is_list_of_signed_member_available == 'No' && $hasUnwantedSignedMemberData) {
            log::info("Unwanted signed member data found in Excel, but it will be rejected as per society settings. Data will not be imported. Please fix the Excel and try again.");
            return [
                'status' => 'error',
                'message' => "Signed member list is selected as No. It must remain empty. Please do not provide Signed member data such as 'Did you purchase the apartment before the society was registered?', 'Did you sign at the time of the society registration?', 'Did the previous owner sign the registration documents?', 'Has the flat transfer-related fee been paid to the Society?', or 'Have physical documents been submitted to the society?'."
            ];
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
            return [
                'status' => 'error',
                'message' => implode(' | ', array_unique($errorMessages))
            ];
        }

        if (!empty($invalidOwnerRows)) {
            return [
                'status' => 'error',
                'message' => "Owner details are invalid (name/mobile empty or mismatch) in rows: " . implode(', ', array_unique($invalidOwnerRows))
            ];
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
            return [
                'status' => 'error',
                'message' => implode(' | ', array_unique($errors))
            ];
        }

        DB::beginTransaction();

        try {
            // timeline
            $timelines = Timeline::orderBy('id')->get();
            $timelineValues = array_values($timelines->pluck('name')->toArray());
            $insertedCount = 0;
            foreach ($validRows as $index => $data) {
                $owner1_name = trim($data[$indexes['owner1_first_name']] . ' ' . ($data[$indexes['owner1_middle_name']] ?? '') . ' ' . ($data[$indexes['owner1_last_name']] ?? ''));
                $owner2_name = trim(($data[$indexes['owner2_first_name']] ?? '') . ' ' . ($data[$indexes['owner2_middle_name']] ?? '') . ' ' . ($data[$indexes['owner2_last_name']] ?? ''));
                $owner3_name = trim(($data[$indexes['owner3_first_name']] ?? '') . ' ' . ($data[$indexes['owner3_middle_name']] ?? '') . ' ' . ($data[$indexes['owner3_last_name']] ?? ''));

                $status = [
                    "tasks" => [
                        [
                            "name" => $timelineValues[0],
                            "responsibilityOf" => "ApartmentOwner",
                            "Status" => "Pending",
                            "createdBy" => "System",
                            "createDateTime" => now(),
                            "updatedBy" => null,
                            "updateDateTime" => null
                        ],
                        [
                            "name" => $timelineValues[1],
                            "responsibilityOf" => "ApartmentOwner",
                            "Status" => "Pending",
                            "createdBy" => null,
                            "createDateTime" => now(),
                            "updatedBy" => null,
                            "updateDateTime" => null,
                        ],
                        [
                            "name" => $timelineValues[2],
                            "responsibilityOf" => "DearSociety",
                            "Status" => "Pending",
                            "createdBy" => "System",
                            "createDateTime" => null,
                            "updatedBy" => null,
                            "updateDateTime" => null
                        ],
                        [
                            "name" => $timelineValues[3],
                            "responsibilityOf" => "DearSociety",
                            "Status" => "Pending",
                            "createdBy" => null,
                            "createDateTime" => null,
                            "updatedBy" => null,
                            "updateDateTime" => null
                        ],
                        [
                            "name" => $timelineValues[4],
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
                        'society_id' => $societyId,
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
            return [
                'status' => 'success',
                'message' => "{$originalFlats} entries inserted successfully!"
            ];
        } else {
            return [
                'status' => 'error',
                'message' => "Society information could not be saved due to some error!"
            ];
        }
    }
}
