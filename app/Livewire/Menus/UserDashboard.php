<?php

namespace App\Livewire\Menus;

use App\Services\UserService;
use Livewire\Component;

class UserDashboard extends Component
{
    public $societyDetail = [];
    public $search = '';
    protected $userService;

    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function render()
    {
        return view('livewire.menus.user-dashboard');
    }

    public function mount()
    {
        $this->societyDetail=$this->userService->getSocietyDetail();
    }

    public function updatedSearch()
    {
        if (strlen($this->search) >= 2 || $this->search === '') {
            $this->societyDetail = $this->userService->getSocietyDetail($this->search);
        }
    }

    public function verifyDetails($apartmentId)
    {
        return redirect()->route('menus.update_society_status',['apartmentId'=>$apartmentId]);
    }
}
