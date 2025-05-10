<?php

namespace App\Livewire\Menus;

use App\Models\Apartments;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use App\Models\Society;
class CreateSociety extends Component
{
    use WithFileUploads;
    public $currentStep = 1;
    
    public $formData = [
        'society_name' => '',
        'address_1' => '',
        'address_2' => '',
        'pincode' => '',
        'state' => '',
        'city' => '',
        'apartments' => [
            ['building_name' => '', 'apartment_number' => '']
        ],
    ];

    public $verification_document;
    // Validation rules for each step
    protected $rules = [
        1 => [
            'formData.society_name' => 'required|string|max:255',
            'formData.address_1' => 'required|string|max:255',
            'formData.address_2' => 'nullable|string|max:255',
            'formData.pincode' => 'required|numeric|digits:6',
            'formData.state' => 'required|string|max:255',
            'formData.city' => 'required|string|max:255'
        ],
        2 => [
            'formData.apartments.*.building_name' => 'required|string|max:255',
            'formData.apartments.*.apartment_number' => 'required',
        ],
        3 => [
            'formData.verification_document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ],
    ];

    public function addApartments()
    {
        $this->formData['apartments'][] = ['building_name' => '', 'apartment_number' => ''];
    }

    public function removeApartments($index)
    {
        // Ensure at least one apartments is present
        if (count($this->formData['apartments']) > 1) {
            unset($this->formData['apartments'][$index]);
            $this->formData['apartments'] = array_values($this->formData['apartments']); // Re-index the array
        }
    }

    public function render()
    {
        return view('livewire.menus.create-society');
    }

    public function nextStep()
    {
        $this->validate($this->rules[$this->currentStep] ?? []);
        $this->currentStep++;
    }

    public function prevStep()
    {
        $this->currentStep--;
    }

    public function save()
    {
        $this->validate($this->rules[$this->currentStep] ?? []);
        // Store file and get path
        if ($this->formData['verification_document']) {
            $filePath = $this->formData['verification_document']->store('society_docs', 'public');
        } else {
            $filePath = null;
        }
    
        $society=Society::create([
            'society_name' => $this->formData['society_name'],
            'address_1' => $this->formData['address_1'],
            'address_2' => $this->formData['address_2'],
            'pincode' => $this->formData['pincode'],
            'state' => $this->formData['state'],
            'city' => $this->formData['city']
        ]);

        if($society){
            foreach ($this->formData['apartments'] as $index => $apartmentData) {
                $result=$society->apartments()->create($apartmentData);
            }
            session()->flash('success', 'Form submitted successfully!');
            $this->reset(['formData', 'verification_document']); ;
            $this->currentStep = 1; // Reset to first step
        }else{
            session()->flash('error', 'Form could not be submitted due to some error!');
        }
    }

}
