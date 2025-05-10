<?php

namespace App\Livewire\Menus;
use App\Models\Society;
use App\Models\Owner;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;

class IssueCertificate extends Component
{
    public $society;

    public function mount($id)
    {
        $user_id=Auth::user()->id;
        $this->society = Society::with([
            'apartments', // apartment_details
            'owners.apartment' // owner → belongsTo → apartment_detail
        ])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.menus.issue-certificate');
    }
}
