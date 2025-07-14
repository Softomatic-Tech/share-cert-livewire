<?php

namespace App\Livewire\Menus;
use App\Models\Society;
use App\Models\SocietyDetail;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;

class IssueCertificate extends Component
{
    public $societyDetails = [];

    public function mount($id)
    {

        $this->societyDetails = SocietyDetail::with('society')->where('id', $id)->first();
    }

    public function render()
    {
        return view('livewire.menus.issue-certificate');
    }
}
