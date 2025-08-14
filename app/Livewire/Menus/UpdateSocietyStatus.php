<?php

namespace App\Livewire\Menus;

use Livewire\Component;
use App\Models\SocietyDetail; 
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class UpdateSocietyStatus extends Component
{
    use WithFileUploads;
    public $apartment;
    public $currentStep = 1;
    public $society_id,$society_name, $total_flats, $address_1, $address_2, $pincode, $city, $state,$apartment_id,$building_name, $apartment_number, $owner1_name, $owner1_mobile ,$owner1_email ,$owner2_name, $owner2_mobile ,$owner2_email ,$owner3_name, $owner3_mobile ,$owner3_email;
    public $agreementCopy,$memberShipForm,$allotmentLetter,$possessionLetter;
    protected $userService;
    public $fileKey;

    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function render()
    {
        return view('livewire.menus.update-society-status');
    }

    public function mount($apartmentId)
    {
        $this->fileKey = now()->timestamp;
        $this->loadSocietyData($apartmentId);
    }

    public function loadSocietyData($apartmentId)
    {
        $apartment = SocietyDetail::with('society')->findOrFail($apartmentId);
        if ($apartment) {
            if ($apartment->society) {
                $this->society_id = $apartment->society->id;
                $this->society_name = $apartment->society->society_name;
                $this->total_flats = $apartment->society->total_flats;
                $this->address_1 = $apartment->society->address_1;
                $this->address_2 = $apartment->society->address_2;
                $this->pincode = $apartment->society->pincode;
                $this->city = $apartment->society->city;
                $this->state = $apartment->society->state;
            }
            $this->apartment_id = $apartment->id;
            $this->building_name = $apartment->building_name;
            $this->apartment_number = $apartment->apartment_number;
            $this->owner1_name = $apartment->owner1_name;
            $this->owner1_mobile = $apartment->owner1_mobile;
            $this->owner1_email = $apartment->owner1_email;
            $this->owner2_name = $apartment->owner2_name;
            $this->owner2_mobile = $apartment->owner2_mobile;
            $this->owner2_email = $apartment->owner2_email;
            $this->owner3_name = $apartment->owner3_name;
            $this->owner3_mobile = $apartment->owner3_mobile;
            $this->owner3_email = $apartment->owner3_email;
            $this->agreementCopy=$apartment->agreementCopy;
            $this->memberShipForm=$apartment->memberShipForm;
            $this->allotmentLetter=$apartment->allotmentLetter;
            $this->possessionLetter=$apartment->possessionLetter;
        }
    }

    public function nextStep()
    {
        if ($this->currentStep == 1) {
            $this->updateSocietyDetails(); 
        }

        if ($this->currentStep === 3) {
            $this->loadSocietyData($this->apartment_id);
        }

        $this->currentStep++;
    }

    public function prevStep()
    {
        $this->currentStep--;
    }
    
    public function updateSocietyDetails()
    {
        $this->validate([
            'society_name' => 'required|string|max:255',
            'total_flats' => 'required|numeric',
            'address_1' => 'required|string|max:255',
            'pincode' => 'required|digits:6',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
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

        $response = $this->userService->updateSocietyDetails(
            [
                'society_name' => $this->society_name,
                'total_flats' => $this->total_flats,
                'address_1' => $this->address_1,
                'address_2' => $this->address_2,
                'pincode' => $this->pincode,
                'state' => $this->state,
                'city' => $this->city,
                'building_name' => $this->building_name,
                'apartment_number' => $this->apartment_number,
                'owner1_name' => $this->owner1_name,
                'owner1_email' => $this->owner1_email,
                'owner1_mobile' => $this->owner1_mobile,
                'owner2_name' => $this->owner2_name,
                'owner2_email' => $this->owner2_email,
                'owner2_mobile' => $this->owner2_mobile,
                'owner3_name' => $this->owner3_name,
                'owner3_email' => $this->owner3_email,
                'owner3_mobile' => $this->owner3_mobile,
            ],
            $this->society_id,
            $this->apartment_id
        );

        if ($response['status']) {
            $this->dispatch('show-success', message:  $response['message']);
            $this->mount($this->apartment_id);
            $this->currentStep = 1;
        } else {
            $this->dispatch('show-error', message:  $response['message'] ?? 'Error updating society');
        }
    }

    public function uploadAgreementCopy()
    {
        $this->validate([
            'agreementCopy' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);

        try {
            $result = $this->userService->uploadSocietyDocument($this->apartment_id, $this->agreementCopy,'agreementCopy','agreementCopy');
            $this->dispatch('show-success', message:  "Agreement Copy uploaded successfully!");
            $this->reset('agreementCopy');
            $this->fileKey = now()->timestamp;
            $this->loadSocietyData($this->apartment_id);
        } catch (\Exception $e) {
        $this->dispatch('show-error', message: 'Something went wrong while uploading the Agreement Copy');
        } 
    }

    public function uploadMemberShipForm()
    {
        $this->validate([
            'memberShipForm' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);
        try {
            $result = $this->userService->uploadSocietyDocument($this->apartment_id, $this->memberShipForm,'memberShipForm','memberShipForm');
            $this->dispatch('show-success', message:  "Membership Form uploaded successfully!");
            $this->reset('memberShipForm');
            $this->fileKey = now()->timestamp;
            $this->loadSocietyData($this->apartment_id);
        } catch (\Exception $e) {
        $this->dispatch('show-error', message: 'Something went wrong while uploading the Membership Form');
        } 
    }

    public function uploadAllotmentLetter()
    {
        $this->validate([
            'allotmentLetter' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);

        try {
            $result = $this->userService->uploadSocietyDocument($this->apartment_id, $this->allotmentLetter, 'allotmentLetter', 'allotmentLetter');
            $this->dispatch('show-success', message:  "Allotment Letter uploaded successfully!");
            $this->reset('allotmentLetter');
            $this->fileKey = now()->timestamp;
            $this->loadSocietyData($this->apartment_id);
        } catch (\Exception $e) {
        $this->dispatch('show-error', message: 'Something went wrong while uploading the Allotment Letter');
        } 
    }

    public function uploadPossessionLetter()
    {
        $this->validate([
            'possessionLetter' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);
        try {
            $result = $this->userService->uploadSocietyDocument($this->apartment_id, $this->possessionLetter, 'possessionLetter', 'possessionLetter');
            $this->dispatch('show-success', message:  "Possession Letter uploaded successfully!");
            $this->reset('possessionLetter');
            $this->fileKey = now()->timestamp;
            $this->loadSocietyData($this->apartment_id);
        } catch (\Exception $e) {
        $this->dispatch('show-error', message: 'Something went wrong while uploading the Possession Letter');
        } 
    }

    public function done()
    {
        $response=$this->userService->updateStatus($this->apartment_id); 
        $this->currentStep = 1;
        if ($response['status']) {
        $this->dispatch('show-success', message:  $response['message']);
        } else {
            $this->dispatch('show-error', message:  $response['message'] ?? 'Error updating society status');
        }
    }
}
