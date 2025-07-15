<?php

namespace App\Livewire\Menus;
use App\Models\SocietyDetail;
use Livewire\Component;

class ViewAllApartments extends Component
{
    public $societyDetails = [];
    public function render()
    {
        return view('livewire.menus.view-all-apartments');
    }

    public function mount()
    {
        $this->societyDetails =SocietyDetail::all();
    }

    public function redirectToCreateApartment()
    {
        return redirect()->route('menus.create_apartment');
    }
}
