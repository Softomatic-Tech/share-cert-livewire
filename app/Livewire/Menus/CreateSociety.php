<?php

namespace App\Livewire\Menus;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
// use Livewire\WithFileUploads;

class CreateSociety extends Component
{
    public $currentStep = 1;
    public $formData = [
        'society_name' => '',
        'address_1' => '',
        'address_2' => '',
        'pincode' => '',
        'state' => '',
        'city' => '',
        'phone' => '',
        'flat_number' => '',
        'owner_name' => '',
        'verification_document' => '',
    ];

    // Validation rules for each step
    protected $rules = [
        1 => [
            'formData.society_name' => 'required',
            'formData.address_1' => 'required',
            'formData.pincode' => 'required|numeric',
            'formData.state' => 'required',
            'formData.city' => 'required',
            'formData.phone' => 'required|digits:10',
        ],
        2 => [
            'formData.flat_number' => 'required',
            'formData.owner_name' => 'required',
        ],
        3 => [
            'formData.verification_document' => 'required',
        ],
    ];

    // **Add Custom Validation Messages**
    protected $messages = [
        'formData.society_name.required' => 'Society Name is required.',
        'formData.address_1.required' => 'Address is required.',
        'formData.pincode.required' => 'Pincode is required.',
        'formData.pincode.numeric' => 'Pincode must be a number.',
        'formData.pincode.digits' => 'Pincode must be exactly 6 digits.',
        'formData.state.required' => 'State is required.',
        'formData.city.required' => 'City is required.',
        'formData.phone.required' => 'Phone number is required.',
        'formData.phone.digits' => 'Phone number must be exactly 10 digits.',
        'formData.flat_number.required' => 'Flat number is required.',
        'formData.owner_name.required' => 'Owner name is required.',
        'formData.verification_document.required' => 'Verification document is required.',
    ];

    public function create_society()
    {
        return view('livewire.menus.create-society');
    }

    public function nextStep()
    {
        log::info('inside next step');
        $this->validate($this->rules[$this->currentStep] ?? []);
        $this->currentStep++;
    }

    public function prevStep()
    {
        log::info('inside previous step');
        $this->currentStep--;
    }

    public function save()
    {
        $this->validate($this->rules[$this->currentStep] ?? []);

        // Store file and get path
        if ($this->verification_document) {
            $filePath = $this->verification_document->store('documents', 'public');
        } else {
            $filePath = null;
        }

        $result=YourModel::create([
            'society_name' => $this->formData['society_name'],
            'address_1' => $this->formData['address_1'],
            'pincode' => $this->formData['pincode'],
            'state' => $this->formData['state'],
            'city' => $this->formData['city'],
            'phone' => $this->formData['phone'],
            'flat_number' => $this->formData['flat_number'],
            'owner_name' => $this->formData['owner_name'],
            'verification_document' => $filePath,
        ]);

        log::info([
            'society_name' => $this->formData['society_name'],
            'address_1' => $this->formData['address_1'],
            'pincode' => $this->formData['pincode'],
            'state' => $this->formData['state'],
            'city' => $this->formData['city'],
            'phone' => $this->formData['phone'],
            'flat_number' => $this->formData['flat_number'],
            'owner_name' => $this->formData['owner_name'],
            'verification_document' => $this->formData['verification_document'],
        ]);

        if($result){
            session()->flash('success', 'Form submitted successfully!');
            $this->reset();
            $this->currentStep = 1; // Reset to first step
        }else{
            session()->flash('error', 'Form could not be submitted due to some error!');
        }
    }

}
