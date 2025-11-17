<?php

namespace App\Services;
use App\Models\Society;
use App\Models\SocietyDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
class UserService
{
    protected $societyDetail = [];
    protected $search;   
    public function getAuthenticatedUser()
    {
        return Auth::user();
    }

    public function getSocietyDetail($search=null){
        $query = SocietyDetail::with('society')
            ->where(function ($query) {
                $userMobile = Auth::user()->phone;
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
        $validator =Validator::make($data, [
            'society_name' => 'required|string|max:255',
            'total_flats' => 'required|numeric',
            'address_1' => 'required|string|max:255',
            'pincode' => 'required|digits:6',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'registration_no' => 'required|string',
            'no_of_shares' => 'required|numeric',
            'share_value' => 'required|numeric|decimal:0,2',
            'building_name' => 'required|string|max:255',
            'apartment_number' => 'required|string|max:50',
            'owner1_name' => 'required|string|max:255',
            'owner1_email' => 'nullable|string|email|max:255',
            'owner1_mobile' => 'required|digits:10',
            'owner2_name' => 'nullable|string|max:255',
            'owner2_email' => 'nullable|string|email|max:255',
            'owner2_mobile' => 'nullable|digits:10',
            'owner3_name' => 'nullable|string|max:255',
            'owner3_email' => 'nullable|string|email|max:255',
            'owner3_mobile' => 'nullable|digits:10',
            'certificate_no' => 'nullable|numeric',
            'individual_no_of_share' => 'nullable|numeric',
                
        ]);

        if ($validator->fails()) {
            return [
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ];
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
            'address_1' => $validated['address_1'],
            'address_2' => $validated['address_2']?? null,
            'pincode' => $validated['pincode'],
            'state_id' => $validated['state_id'],
            'city_id' => $validated['city_id'],
            'registration_no' => $validated['registration_no'],
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
            'owner3_email'      => $validated['owner3_email'] ?? null
        ]);

        return [
            'status'  => true,
            'message' => 'Society and details updated successfully!',
            'data'    => [
                'society'   => $society,
                'apartment' => $apartment
            ]
        ];
    }

    public function uploadSocietyDocument($apartmentId, UploadedFile $file, string $columnName, string $validationField)
    {
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
            // throw ValidationException::withMessages([
            //     $validationField => 'File size should not exceed 2 MB.',
            // ]);
        }

        // Store file
        $fileName = time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('society_docs', $fileName, 'public');

        // Find society detail
        $details = SocietyDetail::find($apartmentId);
        if (!$details) {
            return [
                'status'  => false,
                'message' => 'Society not found.',
            ];
            // throw ValidationException::withMessages([
            //     'apartment_id' => 'Society not found.',
            // ]);
        }

        // Update correct column dynamically
        $details->{$columnName} = $fileName;
        $details->save();

        return [
            'status'  => true,
            'message' => ucfirst(str_replace('_', ' ', $columnName)) . ' uploaded successfully!',
            'file'    => $fileName,
        ];
    }

    public function updateStatus($apartmentId)
    {
        $user=Auth::user();
        $society = SocietyDetail::findOrFail($apartmentId);
        if (!$society) {
            return [
                'status' => false,
                'message' => 'Society not found!'
            ];
        }
        $allDocumentsUploaded = $society->agreementCopy && $society->memberShipForm && $society->allotmentLetter && $society->possessionLetter;
        $status = $society->status; 
        if (is_string($status)) {
            $status = json_decode($status, true);
        }

        foreach ($status['tasks'] as &$task) {
            if ($task['name'] ==='Verify Details') {
                $task['Status'] = 'Approved';
                $task['updatedBy'] = $user->id ?? 'System';
                $task['updateDateTime'] = now();
            }

            if ($task['name'] === 'Application') {
                if ($allDocumentsUploaded) {
                    $task['Status'] = 'Approved';
                    $task['updatedBy'] = $user->id ?? 'System';
                    $task['updateDateTime'] = now();
                } else {
                    $task['Status'] = 'Pending';
                    $task['updatedBy'] = null;
                    $task['updateDateTime'] = null;
                }
            }
        }

        // Save updated JSON
        $society->status = json_encode($status);
        $society->save();
        return [
            'status'  => true,
            'message' => 'Society details and its documents have been verified and submitted successfully!',
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