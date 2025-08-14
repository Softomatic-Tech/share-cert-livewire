<?php

namespace App\Livewire\Menus;
use App\Models\Society;
use App\Models\SocietyDetail;
use App\Models\User;
use Livewire\Component;

class SuperAdminDashboard extends Component
{
    public $totalSocieties,$totalApartments,$totalUsers;
    public function render()
    {
        return view('livewire.menus.super-admin-dashboard');
    }

    public function mount(){
        $this->totalSocieties = Society::count();
        $this->totalApartments = SocietyDetail::count();
        $this->totalUsers = User::count();
    }
}
