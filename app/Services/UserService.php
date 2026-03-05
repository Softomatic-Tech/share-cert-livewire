<?php

namespace App\Services;
use App\Models\Society;
use App\Models\SocietyDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
class UserService
{
    protected $societyDetail = [];
    protected $search;  
    protected $userMobile; 

    public function getSocietyDetail($search=null,$userMobile){
        $query = SocietyDetail::with(['society.state', 'society.city'])
            ->where(function ($query) use ($userMobile) {
                $query->where('owner1_mobile', $userMobile)
                    ->orWhere('owner2_mobile', $userMobile)
                    ->orWhere('owner3_mobile', $userMobile);
            });
            if ($search) {
                $query->where(function ($subQuery) use ($search) {
                    if (is_numeric($search)) {
                        $subQuery->where('id', $search)
                                ->orWhere('apartment_number', $search);
                    } else {
                        $subQuery->where('building_name', 'like', "%{$search}%");
                    }
                });
            } else {
                $query->orderBy('id', 'desc');
            }
            $societyDetail = $search ? collect([$query->first()]) : $query->get();
            return $societyDetail;
    }

    public function updateSocietyDetails(array $data, $society_id,$apartment_id)
    {
        $rules = [
            'society_name' => 'required|string|max:255',
            'total_building' => 'required|numeric',
            'total_flats' => 'required|numeric',
            'address_1' => 'required|string|max:255',
            'pincode' => 'required|digits:6',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'certificate_no' => 'nullable|string',
            'no_of_shares' => 'nullable|numeric',
            'share_capital_amount' => 'nullable|numeric',
            'individual_no_of_share' => 'required|numeric',
            'share_value' => 'required|numeric|decimal:0,2',
            'building_name' => 'required|string|max:255',
            'apartment_number' => 'required|string|max:50',
            'address_2' => 'nullable|string|max:255',
            'owner1_name' => 'required|string|max:255',
            'owner1_email' => 'nullable|string|email|max:255',
            'owner1_mobile' => 'required|digits:10',
            'owner2_name' => 'nullable|string|max:255',
            'owner2_email' => 'nullable|string|email|max:255',
            'owner2_mobile' => 'nullable|digits:10',
            'owner3_name' => 'nullable|string|max:255',
            'owner3_email' => 'nullable|string|email|max:255',
            'owner3_mobile' => 'nullable|digits:10',
            'is_byelaws_available' => 'required|in:yes,no',
        ];

        if (($data['is_byelaws_available'] ?? null) === 'yes') {

            $rules['membership_case'] = 'required|in:case_a,case_b,case_c,case_d';

            if (($data['membership_case'] ?? null) === 'case_a') {
                $rules += [
                    'applicant_name' => 'required|string',
                    'father_husband_name' => 'required|string',
                    'deceased_member_name' => 'required|string',
                    'occupation' => 'required|string',
                    'age' => 'required|numeric',
                    'monthly_income' => 'required|numeric',
                    'residential_addr' => 'required|string',
                    'office_addr' => 'required|string',
                    'flat_area_sq_meters' => 'required',
                    'builder_name' => 'required|string',
                    'other_person_name1' => 'required|string',
                    'other_property_location1' => 'required|string',
                    'other_property_particulars1' => 'required|string',
                    'reason_for_flat1' => 'required|string',
                    'other_person_name2' => 'nullable|string',
                    'other_property_location2' => 'nullable|string',
                    'other_property_particulars2' => 'nullable|string',
                    'reason_for_flat2' => 'nullable|string',
                ];
            }

            if (($data['membership_case'] ?? null) === 'case_b') {
                $rules += [
                    'distinctive_no_from' => 'required',
                    'distinctive_no_to' => 'required',
                    'building_no' => 'required',
                    'flat_area_sq_meters' => 'required',
                    'transferor_name' => 'required',
                    'transferee_name' => 'required',
                    'transfer_fee' => 'required|numeric',
                    'transfer_premium_amount' => 'required|numeric',
                    'transfer_ground_1' => 'required|string',
                    'transfer_ground_2' => 'required|string',
                    'transfer_ground_3' => 'required|string',
                ];
            }

            if (($data['membership_case'] ?? null) === 'case_c') {
                $rules += [
                    'applicant_name' => 'required|string',
                    'deceased_member_name' => 'required|string',
                    'date_of_death' => 'required|date',
                    'society_shares' => 'required|numeric',
                    'occupation' => 'required|string',
                    'age' => 'required|numeric',
                    'monthly_income' => 'required|numeric',
                    'residential_addr' => 'required|string',
                    'office_addr' => 'required|string',
                ];
            }

            if (($data['membership_case'] ?? null) === 'case_d') {
                $rules += [
                    'applicant_name' => 'required|string',
                    'deceased_member_name' => 'required|string',
                    'father_husband_name' => 'required|string',
                    'date_of_death' => 'required|date',
                    'residential_addr' => 'required|string',
                    'inspection_time_from' => 'required',
                    'inspection_time_to' => 'required',
                    'distinctive_no_from' => 'required',
                    'distinctive_no_to' => 'required',
                    'floor_no' => 'required|numeric',
                    'flat_bearing_no' => 'required|numeric',
                    'heir_1_name' => 'required|string',
                    'heir_2_name' => 'nullable|string',
                    'heir_3_name' => 'nullable|string',
                    'heir_4_name' => 'nullable|string',
                    'witness_name' => 'required|string',
                    'witness_address' => 'required|string',
                ];
            }
        }

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $validated = $validator->validated();
        // Update Society
        $society = Society::findOrFail($society_id);
        if (!$society) {
            return [
                'status' => false,
                'message' => 'Society not found!'
            ];
        }
        $society->update([
            'society_name' => $validated['society_name'],
            'total_flats' => $validated['total_flats'],
            'total_building' => $validated['total_building'],
            'address_1' => $validated['address_1'],
            'address_2' => $validated['address_2']?? null,
            'pincode' => $validated['pincode'],
            'state_id' => $validated['state_id'],
            'city_id' => $validated['city_id'],
            'no_of_shares' => $validated['no_of_shares'],
            'share_value' => $validated['share_value'],
        ]);

        // Update Society Details
        $apartment = SocietyDetail::findOrFail($apartment_id);
        if (!$apartment) {
            return [
                'status' => false,
                'message' => 'Society Details not found!'
            ];
        }
        $apartment->update([
            'building_name'     => $validated['building_name'],
            'apartment_number'  => $validated['apartment_number'],
            'certificate_no'    => $validated['certificate_no'],
            'no_of_shares'      => $validated['individual_no_of_share'],
            'share_capital_amount' => $validated['share_capital_amount'],
            'owner1_name'       => $validated['owner1_name'],
            'owner1_mobile'     => $validated['owner1_mobile'],
            'owner1_email'      => $validated['owner1_email'] ?? null,
            'owner2_name'       => $validated['owner2_name'] ?? null,
            'owner2_mobile'     => $validated['owner2_mobile'] ?? null,
            'owner2_email'      => $validated['owner2_email'] ?? null,
            'owner3_name'       => $validated['owner3_name'] ?? null,
            'owner3_mobile'     => $validated['owner3_mobile'] ?? null,
            'owner3_email'      => $validated['owner3_email'] ?? null,
            'is_byelaws_available' => $validated['is_byelaws_available'] ?? 'no',
        ]);

        // Update Bye-Law Case Details
        $apartment->byeLawCase()->updateOrCreate(
            ['society_detail_id' => $apartment->id],
            [
                'membership_case' => $validated['membership_case'] ?? null,
                'applicant_name' => $validated['applicant_name'] ?? null,
                'father_husband_name' => $validated['father_husband_name'] ?? null,
                'age' => $validated['age'] ?? null,
                'monthly_income' => $validated['monthly_income'] ?? null,
                'occupation' => $validated['occupation'] ?? null,
                'office_addr' => $validated['office_addr'] ?? null,
                'residential_addr' => $validated['residential_addr'] ?? null,
                'flat_area_sq_meters' => $validated['flat_area_sq_meters'] ?? null,
                'builder_name' => $validated['builder_name'] ?? null,
                'other_person_name1' => $validated['other_person_name1'] ?? null,
                'other_property_particulars1' => $validated['other_property_particulars1'] ?? null,
                'other_property_location1' => $validated['other_property_location1'] ?? null,
                'reason_for_flat1' => $validated['reason_for_flat1'] ?? null,
                'other_person_name2' => $validated['other_person_name2'] ?? null,
                'other_property_particulars2' => $validated['other_property_particulars2'] ?? null,
                'other_property_location2' => $validated['other_property_location2'] ?? null,
                'reason_for_flat2' => $validated['reason_for_flat2'] ?? null,
                'deceased_member_name' => $validated['deceased_member_name'] ?? null,
                'distinctive_no_from' => $validated['distinctive_no_from'] ?? null,
                'distinctive_no_to' => $validated['distinctive_no_to'] ?? null,
                'transferor_name' => $validated['transferor_name'] ?? null,
                'transferee_name' => $validated['transferee_name'] ?? null,
                'building_no' => $validated['building_no'] ?? null,
                'transfer_fee' => $validated['transfer_fee'] ?? null,
                'transfer_premium_amount' => $validated['transfer_premium_amount'] ?? null,
                'transfer_ground_1' => $validated['transfer_ground_1'] ?? null,
                'transfer_ground_2' => $validated['transfer_ground_2'] ?? null,
                'transfer_ground_3' => $validated['transfer_ground_3'] ?? null,
                'date_of_death' => $validated['date_of_death'] ?? null,
                'society_shares' => $validated['society_shares'] ?? null,
                'inspection_time_from' => $validated['inspection_time_from'] ?? null,
                'inspection_time_to' => $validated['inspection_time_to'] ?? null,
                'floor_no' => $validated['floor_no'] ?? null,
                'flat_bearing_no' => $validated['flat_bearing_no'] ?? null,
                'heir_1_name' => $validated['heir_1_name'] ?? null,
                'heir_2_name' => $validated['heir_2_name'] ?? null,
                'heir_3_name' => $validated['heir_3_name'] ?? null,
                'heir_4_name' => $validated['heir_4_name'] ?? null,
                'witness_name' => $validated['witness_name'] ?? null,
                'witness_address' => $validated['witness_address'] ?? null,
            ]
        );

        return [
            'status'  => true,
            'message' => 'Society and Their Details updated successfully!',
            'data'    => [
                'society'   => $society,
                'apartment' => $apartment
            ]
        ];
    }

    public function uploadSocietyDocument($apartmentId, UploadedFile $file, string $columnName)
    {
        try {
            log::info("Starting document upload for Apartment ID: $apartmentId, Column: $columnName");
            $details = SocietyDetail::find($apartmentId);
            if (!$details) {
                return [
                    'status'  => false,
                    'message' => 'Society not found.',
                ];
            }
            $allowedExtensions = ['jpeg', 'png', 'jpg','pdf'];
            // Validate file type
            if (!in_array(strtolower($file->getClientOriginalExtension()), $allowedExtensions)) {
                return [
                    'status'  => false,
                    'message' => 'Invalid file type , only '.implode(",",$allowedExtensions).' file extension are allowed.',
                ];
            }

            // Validate file size (2 MB limit)
            if ($file->getSize() > 2 * 1024 * 1024) {
                return [
                    'status'  => false,
                    'message' => 'File size should not exceed 2 MB.',
                ];
            }

            // Store file
            $fileName = $columnName.'_' .$apartmentId. '.' . $file->getClientOriginalExtension();
            $file->storeAs('society_docs', $fileName, 'public');        
            log::info("File stored successfully: " . $fileName);
            // Update correct column dynamically
            // Check if ByeLawCase has the column first
            $byeLawCase = $details->byeLawCase()->firstOrCreate(['society_detail_id' => $details->id]);
            if (array_key_exists($columnName, $byeLawCase->getAttributes()) || Schema::hasColumn('bye_law_cases', $columnName)) {
                log::info("Column exists in ByeLawCase, updating: " . $columnName);
                $byeLawCase->{$columnName} = $fileName;
                $byeLawCase->save();
            } else {
                log::info("Column does not exist in ByeLawCase, updating SocietyDetail: " . $columnName);
                $details->{$columnName} = $fileName;
                $details->save();
            }
            log::info("Document upload process completed for Apartment ID: $apartmentId, Column: $columnName");
            return [
                'status'  => true,
                'message' => ucfirst(str_replace('_', ' ', $columnName)) . ' uploaded successfully!',
                'file'    => $fileName,
            ];
        } catch (\Exception $e) {
        return [
            'status'  => false,
            'message' => 'Upload failed: ' . $e->getMessage(),
        ];
    }
    }

    public function updateStatus($apartmentId,$user_id)
    {
        $society = SocietyDetail::findOrFail($apartmentId);
        
        $byeLawCase = $society->byeLawCase;
        $membershipCase = optional($byeLawCase)->membership_case;
        
        $requiredDocs = ['agreementCopy', 'memberShipForm', 'allotmentLetter', 'possessionLetter'];
        if ($membershipCase === 'case_a') {
            $requiredDocs[] = 'allotmentMembershipLetter';
        } elseif ($membershipCase === 'case_b') {
            $requiredDocs = array_merge($requiredDocs, ['stampDutyProof', 'transferorSignature']);
        } elseif ($membershipCase === 'case_c') {
            $requiredDocs = array_merge($requiredDocs, ['deathCertificate', 'nominationRecord']);
        } elseif ($membershipCase === 'case_d') {
            $requiredDocs[] = 'successionCertificate';
        }

        $allDocumentsUploaded = true;
        foreach ($requiredDocs as $doc) {
            $uploaded = false;
            if (!empty($society->$doc)) {
                $uploaded = true;
            } elseif ($byeLawCase && !empty($byeLawCase->$doc)) {
                $uploaded = true;
            }

            if (!$uploaded) {
                $allDocumentsUploaded = false;
                break;
            }
        }
        
        $status = $society->status; 
        if (is_string($status)) {
            $status = json_decode($status, true);
        }

        $anyStatusChanged = false;
        $applicationStatusChanged = false;

        foreach ($status['tasks'] as &$task) {
            $oldTaskStatus = $task['Status'] ?? null;
            
            if ($task['name'] === 'Verify Details') {
                if ($oldTaskStatus !== 'Approved') {
                    $task['Status'] = 'Approved';
                    $task['updatedBy'] = $user_id ?? 'System';
                    $task['updateDateTime'] = now();
                    $anyStatusChanged = true;
                }
            }

            if ($task['name'] === 'Application') {
                $newTaskStatus = $allDocumentsUploaded ? 'Approved' : 'Pending';
                if ($oldTaskStatus !== $newTaskStatus) {
                    $task['Status'] = $newTaskStatus;
                    $task['updatedBy'] = $allDocumentsUploaded ? ($user->id ?? 'System') : null;
                    $task['updateDateTime'] = $allDocumentsUploaded ? now() : null;
                    $anyStatusChanged = true;
                    $applicationStatusChanged = true;
                }
            }
        }

        if ($anyStatusChanged) {
            $society->status = json_encode($status);
            $society->save();
        }

        if ($allDocumentsUploaded) {
            if ($applicationStatusChanged) {
                return [
                    'status' => true,
                    'message' => 'All documents verified. Society status updated to Approved.',
                ];
            }
            return [
                'status' => true,
                'message' => 'Society status is already Approved.',
            ];
        }

        return [
            'status' => false,
            'message' => 'Missing documents. Status remains Pending.',
        ];
    }

    public function checkFileApproval($data)
    {
        // Validate input
        $validator =Validator::make($data, [
            'statusData' => 'required|array',
            'fileName' => 'required|string'
        ]);

        $statusData = $validator['statusData'];
        $fileName = $validator['fileName'];

        // Check approval
        $isApproved = false;

        foreach ($statusData['tasks'] as $task) {
            if ($task['name'] === 'Application') {
                foreach ($task['subtasks'] ?? [] as $subtask) {
                    if (
                        isset($subtask['fileName'], $subtask['status']) &&
                        trim($subtask['fileName']) === trim($fileName) &&
                        $subtask['status'] === 'Approved'
                    ) {
                        $isApproved = true;
                        break 2; // Exit both loops
                    }
                }
            }
        }

        // Return JSON response
        if ($isApproved) {
            return true;
        } else {
            return false;
            
        }
    }
}