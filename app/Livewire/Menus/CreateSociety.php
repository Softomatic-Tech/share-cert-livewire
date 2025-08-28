<?php

namespace App\Livewire\Menus;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use App\Models\Society;
use App\Models\State;
use App\Models\City;

class CreateSociety extends Component
{
    public $society_name, $address_1, $address_2, $pincode, $state_id, $city_id, $total_flats;
    public $states, $cities=[];
    protected $validationAttributes = [
        'state_id' => 'state',
        'city_id'  => 'city',
    ];
    public function render()
    {
        return view('livewire.menus.create-society');
    }

    public function mount(){
        $this->states=State::all();
    }

    public function updatedStateID($value)
    {
        $this->cities = City::where('state_id', $value)->get();
    }

    public function saveSociety()
    {
        $validated = $this->validate([
            'society_name' => 'required|string|max:255',
            'address_1' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'pincode' => 'required|numeric|digits:6',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'total_flats' => 'required|numeric',
        ]);
        $society=Society::create($validated);
        if($society){
            $this->dispatch('show-success', message: 'Society information saved successfully!');
            $this->reset(['society_name', 'address_1', 'address_2', 'pincode', 'state_id', 'city_id', 'total_flats']);
        }else{
            $this->dispatch('show-error', message: 'Society information could not be saved due to some error!');
        }
        
    }
}
