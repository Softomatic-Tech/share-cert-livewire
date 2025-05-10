<?php

namespace App\Livewire\Menus;
use App\Models\Owner;
use App\Models\ApartmentDetail;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

class RegisterSociety extends Component
{
    use WithFileUploads;
    public $apartment_detail_id;

    public $society = []; // list of societies
    public $buildingOptions = []; // options populated based on selected society
    public $flatOptions = []; 

    public $currentStep = 1;
    public $formSaved = false; // Add this flag
    public $ownerIds=[];
    public $formData = [
        'society_id'=>'',
        'building_id'=>'',
        'apartment_detail_id'=>'',
        'owners' => [
            ['owner_name' => '', 'email' => '', 'phone' => '']
        ],
    ];

    public function render()
    {
        return view('livewire.menus.register-society');
    }

    // Load existing owner data when going back to step 1
    public function mount($existingOwnerIds  = [])
    {
        $this->society=DB::table('societies')->get();
        // If ownerId is passed (when editing), load the existing data
        if (!empty($existingOwnerIds)) {
            $this->ownerIds = $existingOwnerIds;
            $this->loadOwnersData();
        }
    }

    public function updatedFormDataSocietyId($value)
    {
        $this->buildingOptions = ApartmentDetail::where('society_id', $value)->select('id', 'building_name')->get();
        $this->apartment_detail_id = '';
        $this->flatOptions = [];
    }

    public function updatedFormDataBuildingId($value)
    {
        $this->flatOptions = ApartmentDetail::where('id', $value)->select('id','apartment_number')
            ->get();

        $this->apartment_detail_id = '';
    }

    public function addOwner()
    {
        // Ensure no more than 4 owners are added
        if (count($this->formData['owners']) < 4) {
            $this->formData['owners'][] = ['owner_name' => '', 'email' => '', 'phone' => ''];
        }
    }

    public function removeOwner($index)
    {
        // Ensure at least one owner is present
        if (count($this->formData['owners']) > 1) {
            unset($this->formData['owners'][$index]);
            $this->formData['owners'] = array_values($this->formData['owners']); // Re-index the array
        }
    }

    public function nextStep()
    {
        if($this->currentStep==4){
            session()->flash('success', 'Form submitted successfully!');
            $this->reset(['formData']);
            $this->currentStep = 1;
        }else{
            // $this->validate($this->rules[$this->currentStep] ?? []);
            if (!$this->formSaved) {
                $this->save();
            }
            $this->currentStep++;
        }
    }

    public function prevStep()
    {
        $this->currentStep--;
    }

    // Load existing owners' data from database
    public function loadOwnersData()
    {
        $owners = Owner::whereIn('id', $this->ownerIds)->get();
        $this->formData['owners'] = $owners->map(function ($owner) {
            return [
                'owner_name' => $owner->owner_name,
                'email' => $owner->email,
                'phone' => $owner->phone
            ];
        })->toArray();
        $this->formSaved = true;
    }

    public function save()
    {
        $this->validate([
            'formData.society_id'=>'required',
            'formData.building_id'=>'required',
            'formData.apartment_detail_id' => 'required',
            'formData.owners.*.owner_name' => 'required|string|max:255',
            'formData.owners.*.email' => 'required|email',
            'formData.owners.*.phone' => 'required|numeric|digits:10'
        ]);
    
        $newOwnerIds = [];
        if ($this->formSaved) {
            log::info('form already saved');
            log::info($this->formData['owners']);
            // If the form is saved, update the existing owner data instead of creating new
            foreach ($this->formData['owners'] as $index => $ownerData) {
                $ownerData['user_id']=Auth::user()->id;
                $ownerData['apartment_detail_id']=$this->formData['apartment_detail_id'];
                if (isset($this->ownerIds[$index]) && $this->ownerIds[$index]) {
                    log::info('owner id -'.$this->ownerIds[$index]);
                    Owner::where('id', $this->ownerIds[$index])->update($ownerData);
                    $newOwnerIds[] = $this->ownerIds[$index];
                } else {
                    $newOwner = Owner::create($ownerData);
                    $newOwnerIds[] = $newOwner->id; // Store new owner ID
                }
            }

            // Delete removed owners from the database
            $ownersToDelete = array_diff($this->ownerIds, $newOwnerIds);
            if (!empty($ownersToDelete)) {
                Owner::whereIn('id', $ownersToDelete)->delete();
            }

            // Update owner IDs to match formData
            $this->ownerIds = $newOwnerIds;
            $this->currentStep = 2; // Move to next step
        } else {
            $c=1;
            $this->ownerIds = [];
            foreach ($this->formData['owners'] as $owner) {
                $owner['user_id']=Auth::user()->id;
                $owner['apartment_detail_id']=$this->formData['apartment_detail_id'];
                $newOwner = Owner::create($owner);
                $this->ownerIds[] = $newOwner->id;          
                $c++;
            }
        }
        // Mark the form as saved
        $this->formSaved = true;
        $this->currentStep = 2;
    }
}
