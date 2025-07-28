<?php

namespace App\Livewire\Menus;

use Livewire\Component;
use App\Models\Society;
use App\Models\SocietyDetail; 
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UpdateSocietyStatus extends Component
{
    use WithFileUploads;
    public $apartment;
    public $currentStep = 1;
    public $society_id,$society_name, $total_flats, $address_1, $address_2, $pincode, $city, $state,$apartment_id,$building_name, $apartment_number, $owner1_name, $owner1_mobile ,$owner1_email ,$owner2_name, $owner2_mobile ,$owner2_email ,$owner3_name, $owner3_mobile ,$owner3_email;
    public $agreementCopy,$memberShipForm,$allotmentLetter,$possessionLetter;
    public function render()
    {
        return view('livewire.menus.update-society-status');
    }

    public function mount($apartmentId)
    {
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

        // Update Society
        $society = Society::findOrFail($this->society_id);
        $society->update([
            'society_name' => $this->society_name,
            'total_flats' => $this->total_flats,
            'address_1' => $this->address_1,
            'address_2' => $this->address_2,
            'pincode' => $this->pincode,
            'state' => $this->state,
            'city' => $this->city,
        ]);

        // Update Society Details
        $apartment = SocietyDetail::findOrFail($this->apartment_id);
        $apartment->update([
            'building_name'     => $this->building_name,
            'apartment_number'  => $this->apartment_number,
            'owner1_name'       => $this->owner1_name,
            'owner1_mobile'     => $this->owner1_mobile,
            'owner1_email'      => $this->owner1_email,
            'owner2_name'       => $this->owner2_name,
            'owner2_mobile'     => $this->owner2_mobile,
            'owner2_email'      => $this->owner2_email,
            'owner3_name'       => $this->owner3_name,
            'owner3_mobile'     => $this->owner3_mobile,
            'owner3_email'      => $this->owner3_email,
        ]);

        if($society || $apartment){
        session()->flash('success', 'Society and details updated successfully!');
        $this->mount($this->apartment_id);
        $this->currentStep = 1; // Reset to first step
        }else{
            session()->flash('error', 'Society information could not be saved due to some error!');
        }
    }

    public function uploadAgreementCopy()
    {
        $this->validate([
            'agreementCopy' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);

        $fileName = time().'.'.$this->agreementCopy->getClientOriginalExtension();
        $path = $this->agreementCopy->storeAs('society_docs', $fileName, 'public');
        $details = SocietyDetail::find($this->apartment_id);
        if ($details) {
            $details->agreementCopy = $fileName;
            $details->save();

            session()->flash('success', 'Agreement Copy uploaded successfully!');
        } else {
            session()->flash('error', 'Society not found.');
        }
        $this->reset('agreementCopy');
        $this->loadSocietyData($this->apartment_id);
    }

    public function uploadMemberShipForm()
    {
        $this->validate([
            'memberShipForm' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);

        $fileName = time().'.'.$this->memberShipForm->getClientOriginalExtension();
        $path = $this->memberShipForm->storeAs('society_docs', $fileName, 'public');
        $details = SocietyDetail::find($this->apartment_id);
        if ($details) {
            $details->memberShipForm = $fileName;
            $details->save();

            session()->flash('success', 'Membership Form uploaded successfully!');
        } else {
            session()->flash('error', 'Society not found.');
        }
        $this->reset('memberShipForm');
        $this->loadSocietyData($this->apartment_id);
    }

    public function uploadAllotmentLetter()
    {
        $this->validate([
            'allotmentLetter' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);

        $fileName = time().'.'.$this->allotmentLetter->getClientOriginalExtension();
        $path = $this->allotmentLetter->storeAs('society_docs', $fileName, 'public');
        $details = SocietyDetail::find($this->apartment_id);
        if ($details) {
            $details->allotmentLetter = $fileName;
            $details->save();

            session()->flash('success', 'Allotment Letter uploaded successfully!');
        } else {
            session()->flash('error', 'Society not found.');
        }
        $this->reset('allotmentLetter');
        $this->loadSocietyData($this->apartment_id);
    }

    public function uploadPossessionLetter()
    {
        $this->validate([
            'possessionLetter' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);

        $fileName = time().'.'.$this->possessionLetter->getClientOriginalExtension();
        $path = $this->possessionLetter->storeAs('society_docs', $fileName, 'public');
        $details = SocietyDetail::find($this->apartment_id);
        if ($details) {
            $details->possessionLetter = $fileName;
            $details->save();

            session()->flash('success', 'Possession Letter uploaded successfully!');
        } else {
            session()->flash('error', 'Society not found.');
        }
        $this->reset('possessionLetter');
        $this->loadSocietyData($this->apartment_id);
    }

    public function updateStatus($apartmentId)
    {
        $user=Auth::user();
        $society = SocietyDetail::findOrFail($apartmentId);
        $allDocumentsUploaded = $society->agreementCopy && $society->memberShipForm && $society->allotmentLetter && $society->possessionLetter;
        $status = $society->status; 
        if (is_string($status)) {
            $status = json_decode($status, true);
        }

        foreach ($status['tasks'] as &$task) {
            if ($task['name'] ==='Verify Details') {
                $task['Status'] = 'Applied';
                $task['updatedBy'] = $user->id ?? 'System';
                $task['updateDateTime'] = now();
            }

            if ($task['name'] === 'Application') {
                if ($allDocumentsUploaded) {
                    $task['Status'] = 'Applied';
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
    }

    public function done()
    {
        $this->updateStatus($this->apartment_id); 

        $this->currentStep = 1;
        session()->flash('success', 'Society details and its documents have been verified and submitted successfully!');
    }
}
