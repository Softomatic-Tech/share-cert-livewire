<?php

namespace App\Livewire\Menus;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Models\Society;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Facades\Auth;

class CreateSociety extends Component
{
    public $society_name, $address_1, $address_2, $pincode, $state_id, $city_id, $total_building, $total_flats, $registration_no, $no_of_shares, $share_value;
    // $i_register, $j_register;
    public $states, $cities = [];
    protected $validationAttributes = [
        'state_id' => 'state',
        'city_id'  => 'city',
    ];
    public $is_list_of_signed_member_available = 'No';
    public $is_byelaws_available = 'No';

    public function render()
    {
        return view('livewire.menus.create-society');
    }

    public function mount()
    {
        $this->states = State::orderBy('name', 'asc')->get();
        $this->state_id = 32; // Maharashtra
        $this->cities = City::where('state_id', $this->state_id)->get();
    }

    public function updatedStateID($value)
    {
        $this->cities = City::where('state_id', $value)->get();
    }

    public function updatedFormDataStateID($stateId)
    {
        $this->cities = City::where('state_id', $stateId)->get();
        $this->formData['city'] = null;
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
            'total_building' => 'required|numeric',
            'total_flats' => 'required|numeric',
            'registration_no' => 'required|string',
            'no_of_shares' => 'required|numeric',
            'share_value' => 'required|numeric|decimal:0,2',
            'is_list_of_signed_member_available' => 'required|in:Yes,No',
            'is_byelaws_available' => 'required|in:Yes,No',
            // 'i_register' => 'nullable|string|max:255',
            // 'j_register' => 'nullable|string|max:255',
        ]);

        // Set admin_id to current authenticated user
        $validated['admin_id'] = Auth::id();

        $society = Society::create($validated);
        if ($society) {
            $this->dispatch('show-success', message: 'Society information saved successfully!');
            $this->reset(['society_name', 'address_1', 'address_2', 'pincode', 'state_id', 'city_id', 'total_building', 'total_flats', 'registration_no', 'no_of_shares', 'share_value', 'is_list_of_signed_member_available', 'is_byelaws_available']);
        } else {
            $this->dispatch('show-error', message: 'Society information could not be saved due to some error!');
        }
    }
}
