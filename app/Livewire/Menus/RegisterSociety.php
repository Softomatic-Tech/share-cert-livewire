<?php

namespace App\Livewire\Menus;
use App\Models\Society;
use App\Models\SocietyDetail;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

class RegisterSociety extends Component
{

    public $selectedSociety  = null; 
    public $selectedBuilding = null;
    public $selectedApartment = null;
    public $society = [];
    public $buildings = [];
    public $apartments = [];

    public $currentStep = 1;
    public $formSaved = false; // Add this flag
    
    public $formData = [
        'society_id'=>'',
    ];

    public function render()
    {
        return view('livewire.menus.register-society');
    }

    // Load existing owner data when going back to step 1
    public function mount()
    {
        $this->society =Society::all();
    }

    public function updatedSelectedSociety($societyId)
    {
        $this->buildings = SocietyDetail::where('society_id', $societyId)->select('building_name')->distinct()->get();
        $this->selectedBuilding = null;
        $this->selectedApartment = null;
        $this->apartments = [];
    }

    public function updatedSelectedBuilding($buildingName)
    {
        if ($this->selectedSociety && $buildingName) {
            $this->apartments = SocietyDetail::where('society_id', $this->selectedSociety)->where('building_name', $buildingName)->select('apartment_number')->distinct()->get();
        }
        $this->selectedApartment = null;
    }

}
