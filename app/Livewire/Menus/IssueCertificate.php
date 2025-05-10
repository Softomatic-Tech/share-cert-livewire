<?php

namespace App\Livewire\Menus;
use App\Models\ApartmentDetail;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;

class IssueCertificate extends Component
{
    public $apartments;

    public function mount($id)
    {
        $user_id=Auth::user()->id;
        $this->apartments = ApartmentDetail::with([
            'society:id,society_name',
            'owners' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            }
            ])->where('id', $id)->first();
    }

    public function render()
    {
        return view('livewire.menus.issue-certificate');
    }
}
