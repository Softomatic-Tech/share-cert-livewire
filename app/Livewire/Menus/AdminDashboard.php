<?php

namespace App\Livewire\Menus;

use App\Models\User;
use App\Models\Role;
use App\Models\Society;
use App\Models\SocietyDetail;
use Livewire\Component;
use Illuminate\Http\RedirectResponse;

class AdminDashboard extends Component
{
    public $users;
    public $adminRole;
    public $userRole;
    public $societyCount;
    public $societyDetailsCount;
    public $issueCertificateCount;

    public function render()
    {
        return view('livewire.menus.admin-dashboard');
    }

    public function mount(){
        $this->users=User::orderBy('id','desc')->get();
        $this->adminRole=Role::where('role','Admin')->value('id');
        $this->userRole=Role::where('role','Society User')->value('id');
        $this->societyCount = Society::count();
        $this->societyDetailsCount = SocietyDetail::count();
        $this->issueCertificateCount=100;
    }
    
    public function markRole($userID,$roleID){
        $user = User::findOrFail($userID);
        $user->role_id = $roleID;
        $user->save();
        if($user)
            session()->flash('success', 'Role updated successfully!');
        else
            session()->flash('error', 'Role not updated!');
    }

    public function redirectToSocietyPage()
    {
        return redirect()->route('admin.view-societies');
    }

    public function redirectToApartmentPage()
    {
        return redirect()->route('admin.view-apartments');
    }
}
