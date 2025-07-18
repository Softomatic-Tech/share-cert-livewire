<?php

namespace App\Livewire\Menus;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use App\Models\Society;
use App\Models\SocietyDetail;

class CreateSociety extends Component
{
    public $society_name, $address_1, $address_2, $pincode, $state, $city, $total_flats;

    public function render()
    {
        return view('livewire.menus.create-society');
    }

    public function saveSociety()
    {
        $validated = $this->validate([
            'society_name' => 'required|string|max:255',
            'address_1' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'pincode' => 'required|numeric|digits:6',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'total_flats' => 'required|numeric',
        ]);
        $society=Society::create($validated);
        if($society){
            session()->flash('success', 'Society information saved successfully!');
            $this->reset(['society_name', 'address_1', 'address_2', 'pincode', 'state', 'city', 'total_flats']);
        }else{
            session()->flash('error', 'Society information could not be saved due to some error!');
        }
        
    }

    public function redirectToSocietyPage()
    {
        return redirect()->route('admin.view-societies');
    }

}
