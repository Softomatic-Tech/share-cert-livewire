<?php

namespace App\Services;
use App\Models\Society;
use App\Models\SocietyDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserService
{
    protected $societyDetail = [];
    protected $search;   
    public function getAuthenticatedUser()
    {
        return Auth::user();
    }

    public function getSocietyDetail(?string $search = null){
        $societyDetail = SocietyDetail::with('society')
            ->where(function ($query) {
                $userMobile = Auth::user()->phone;
                $query->where('owner1_mobile', $userMobile)
                    ->orWhere('owner2_mobile', $userMobile)
                    ->orWhere('owner3_mobile', $userMobile);
            })
            ->when($search && strlen($search) >= 2, function ($query) use ($search) {
                $query->whereHas('society', function ($q) use ($search) {
                    $q->where('society_name', 'like', '%' . $search . '%');
                });
            })
            ->get();
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
            // throw ValidationException::withMessages([
            //     $validationField => 'Invalid file type.',
            // ]);
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
}