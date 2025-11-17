<?php

namespace App\Livewire\Menus;

use Livewire\Component;
use App\Models\Society; 
use App\Models\SocietyDetail; 
use App\Models\State;
use App\Models\City;
use App\Services\UserService;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class UpdateSocietyStatus extends Component
{
    use WithFileUploads;
    public $apartment,$state,$city;
    public $states, $cities=[];
    public $currentStep = 1;
    public $society_id,$society_name, $total_flats, $address_1, $address_2, $pincode, $city_id, $state_id,$registration_no,$no_of_shares,$share_value,$apartment_id,$building_name, $apartment_number,$certificate_no,$individual_no_of_share, $share_capital_amount,$owner1_name, $owner1_mobile ,$owner1_email ,$owner2_name, $owner2_mobile ,$owner2_email ,$owner3_name, $owner3_mobile ,$owner3_email;
    public $agreementCopy,$memberShipForm,$allotmentLetter,$possessionLetter;
    public $newAgreementCopy, $newMemberShipForm, $newAllotmentLetter, $newPossessionLetter;
    protected $userService;
    public $fileKey;
    public $agreementUploaded =false;
    public $membershipUploaded=false;
    public $allotmentUploaded=false;
    public $possessionUploaded= false;
    public $approvedFiles;

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
        $this->states=State::all();
        $this->fileKey = now()->timestamp;
        $this->loadSocietyData($apartmentId);
    }

    public function updatedStateID($value)
    {
        $this->cities = City::where('state_id', $value)->get();
    }

    public function loadSocietyData($apartmentId)
    {
        $apartment = SocietyDetail::with(['society.state','society.city'])->findOrFail($apartmentId);
        if ($apartment) {
            if ($apartment->society) {
                $this->society_id = $apartment->society->id;
                $this->society_name = $apartment->society->society_name;
                $this->total_flats = $apartment->society->total_flats;
                $this->address_1 = $apartment->society->address_1;
                $this->address_2 = $apartment->society->address_2;
                $this->pincode = $apartment->society->pincode;
                $this->state_id = $apartment->society->state_id;
                $this->cities = City::where('state_id', $this->state_id)->get();
                $this->city_id = $apartment->society->city_id;
                $this->state = $apartment->society->state->name;
                $this->city = $apartment->society->city->name;
                $this->registration_no = $apartment->society->registration_no;
                $this->no_of_shares = $apartment->society->no_of_shares;
                $this->share_value = $apartment->society->share_value;
            }
            $this->apartment_id = $apartment->id;
            $this->building_name = $apartment->building_name;
            $this->apartment_number = $apartment->apartment_number;
            $this->certificate_no = $apartment->certificate_no;
            $this->individual_no_of_share = $apartment->no_of_shares;
            $this->share_capital_amount = $apartment->share_capital_amount;
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
            $statusData = json_decode($apartment->status, true);

            // prepare approved files array
            $this->approvedFiles = [];
            if (!empty($statusData['tasks'])) {
                foreach ($statusData['tasks'] as $task) {
                    if ($task['name'] === 'Application') {
                        foreach ($task['subtasks'] ?? [] as $subtask) {
                            if ($subtask['status'] === 'Approved') {
                                $this->approvedFiles[] = $subtask['fileName'];
                            }
                        }
                    }
                }
            }
        }
    }

    public function isFileApproved($statusData, $fileName)
    {
        $data = [
            'statusData' => $statusData,
            'fileName' => $fileName,
        ];
        $response=$this->userService->checkFileApproval($data);
        return $response;
    }


    public function nextStep()
    {
        if ($this->currentStep == 1) {
            $society = Society::find($this->society_id);
            if (empty($society->no_of_shares) || empty($society->share_value)) {
                $this->dispatch('show-error', message: "No of shares or share amount is not set. Unable to verify society details");
                return;
            }else{
                $this->updateSocietyDetails(); 
            }
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

        $response = $this->userService->updateSocietyDetails(
            [
                'society_name' => $this->society_name,
                'total_flats' => $this->total_flats,
                'address_1' => $this->address_1,
                'address_2' => $this->address_2,
                'pincode' => $this->pincode,
                'state_id' => $this->state_id,
                'city_id' => $this->city_id,
                'registration_no' => $this->registration_no,
                'no_of_shares' => $this->no_of_shares,
                'share_value' => $this->share_value,
                'building_name' => $this->building_name,
                'apartment_number' => $this->apartment_number,
                'certificate_no' => $this->certificate_no,
                'individual_no_of_share' => $this->individual_no_of_share,
                'share_capital_amount' => $this->share_capital_amount,
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
            'newAgreementCopy' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ],
        [],
        [
            'newAgreementCopy' => 'Agreement Copy', // custom label
        ]
    );

        try {
            $result = $this->userService->uploadSocietyDocument($this->apartment_id, $this->newAgreementCopy,'agreementCopy','newAgreementCopy');
            $this->dispatch('show-success', message:  "Agreement Copy uploaded successfully!");
            $this->agreementUploaded = true;
            $this->reset('newAgreementCopy');
            $this->fileKey = now()->timestamp;
            $this->loadSocietyData($this->apartment_id);
        } catch (\Exception $e) {
        $this->dispatch('show-error', message: 'Something went wrong while uploading the Agreement Copy');
        } 
    }

    public function uploadMemberShipForm()
    {
        $this->validate([
            'newMemberShipForm' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ],
        [],
        [
            'newMemberShipForm' => 'MemberShip Form', // custom label
        ]
    );
        try {
            $result = $this->userService->uploadSocietyDocument($this->apartment_id, $this->newMemberShipForm,'memberShipForm','newMemberShipForm');
            $this->dispatch('show-success', message:  "Membership Form uploaded successfully!");
            $this->membershipUploaded = true;
            $this->reset('newMemberShipForm');
            $this->fileKey = now()->timestamp;
            $this->loadSocietyData($this->apartment_id);
        } catch (\Exception $e) {
        $this->dispatch('show-error', message: 'Something went wrong while uploading the Membership Form');
        } 
    }

    public function uploadAllotmentLetter()
    {
        $this->validate([
            'newAllotmentLetter' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ],
        [],
        [
            'newAllotmentLetter' => 'Allotment Letter', // custom label
        ]
    );

        try {
            $result = $this->userService->uploadSocietyDocument($this->apartment_id, $this->newAllotmentLetter, 'allotmentLetter', 'newAllotmentLetter');
            $this->dispatch('show-success', message:  "Allotment Letter uploaded successfully!");
            $this->allotmentUploaded = true;
            $this->reset('newAllotmentLetter');
            $this->fileKey = now()->timestamp;
            $this->loadSocietyData($this->apartment_id);
        } catch (\Exception $e) {
        $this->dispatch('show-error', message: 'Something went wrong while uploading the Allotment Letter');
        } 
    }

    public function uploadPossessionLetter()
    {
        $this->validate([
            'newPossessionLetter' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ],
        [],
        [
            'newPossessionLetter' => 'Possession Letter', // custom label
        ]
    );
        try {
            $result = $this->userService->uploadSocietyDocument($this->apartment_id, $this->newPossessionLetter, 'possessionLetter', 'possessionLetter');
            $this->dispatch('show-success', message:  "Possession Letter uploaded successfully!");
            $this->possessionUploaded = true;
            $this->reset('newPossessionLetter');
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
        // $this->dispatch('show-success', message:  $response['message']);
        return redirect()->route('user.dashboard');
        } else {
            $this->dispatch('show-error', message:  $response['message'] ?? 'Error updating society status');
        }
    }
}
