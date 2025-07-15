<?php

namespace App\Livewire\Menus;
use App\Models\Society;
use Livewire\Component;

class ViewAllSocieties extends Component
{
    public $societies=[];
    public function render()
    {
        return view('livewire.menus.view-all-societies');
    }

    public function mount()
    {
        $this->societies =Society::all();
    }

    public function redirectToCreateSociety()
    {
        return redirect()->route('menus.create_society');
    }
}
