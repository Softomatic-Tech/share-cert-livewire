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
    public $pendingApplication,$pendingApplicationCount,$pendingVerification,$pendingVerificationCount,$rejectedVerification,$rejectedVerificationCount;
    public $issueCertificateCount;

    public function render()
    {
        return view('livewire.menus.admin-dashboard');
    }

    public function mount(){
        $this->users=User::orderBy('id','desc')->get();
        $this->adminRole=Role::where('role','Admin')->value('id');
        $this->userRole=Role::where('role','Society User')->value('id');

        $this->pendingApplication = SocietyDetail::get()
            ->filter(function ($item) {
            $json = json_decode($item->status, true);
            if (!isset($json['tasks'])) return false;
            $tasks = collect($json['tasks']);
            $verify = $tasks->firstWhere('name', 'Verify Details');
            $application = $tasks->firstWhere('name', 'Application');
            $verification = $tasks->firstWhere('name', 'Verification');
            return (
                ($verify && $verify['Status'] === 'Pending') &&
                ($application && $application['Status'] === 'Pending') &&
                ($verification && $verification['Status'] === 'Pending')
            );
            
            return false;
        })
        ->unique('society_id');
    $this->pendingApplicationCount=$this->pendingApplication->count();

    $this->pendingVerification = SocietyDetail::get()
        ->filter(function ($item) {
        $json = json_decode($item->status, true);
        if (!isset($json['tasks'])) return false;
        $tasks = collect($json['tasks']);
        $verify = $tasks->firstWhere('name', 'Verify Details');
        $application = $tasks->firstWhere('name', 'Application');
        $verification = $tasks->firstWhere('name', 'Verification');
        return (
            $verify && $verify['Status'] === 'Applied' &&
            $application && $application['Status'] === 'Applied' &&
            $verification && $verification['Status'] === 'Pending'
        );
        
    })
    ->unique('society_id');
    $this->pendingVerificationCount=$this->pendingVerification->count();

    $this->rejectedVerification = SocietyDetail::get()
        ->filter(function ($item) {
        $json = json_decode($item->status, true);
        if (!isset($json['tasks'])) return false;
        $tasks = collect($json['tasks']);
        $verify = $tasks->firstWhere('name', 'Verify Details');
        $application = $tasks->firstWhere('name', 'Application');
        $verification = $tasks->firstWhere('name', 'Verification');
        return (
            $verify && $verify['Status'] === 'Pending' &&
            $application && $application['Status'] === 'Pending' &&
            $verification && $verification['Status'] === 'Rejected'
        );
        
    })
    ->unique('society_id');
    $this->rejectedVerificationCount=$this->rejectedVerification->count();
    $this->issueCertificateCount=100;
    }
    
    public function markRole($userID,$roleID){
        $user = User::findOrFail($userID);
        $user->role_id = $roleID;
        $user->save();
        if($user)
            $this->dispatch('showSuccess', message: 'Role updated successfully!');
        else
            $this->dispatch('showError', message: 'Role not updated!');
    }

    public function redirectToSociety($status)
    {
        return redirect()->route('admin.view-societies',['societyStatus'=>$status]);
    }

    public function redirectToApartmentPage()
    {
        return redirect()->route('admin.view-apartments');
    }
}
