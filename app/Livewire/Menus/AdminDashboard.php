<?php

namespace App\Livewire\Menus;

use App\Models\User;
use App\Models\Role;
use App\Models\Society;
use App\Models\SocietyDetail;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;

class AdminDashboard extends Component
{
    use WithPagination;
    public $society;
    public $adminRole,$userRole;
    public $pendingApplication,$pendingApplicationCount,$pendingVerification,$pendingVerificationCount,$rejectedVerification,$rejectedVerificationCount;
    public $pendingVerificationStatus,$approvedVerificationStatus,$rejectedVerificationStatus;
    public $pendingVerificationStatusCount=0;
    public $approvedVerificationStatusCount=0;
    public $rejectedVerificationStatusCount=0;
    public $issueCertificateCount,$usersCount;
    public $selectedOption;

    public function render()
    {
        return view('livewire.menus.admin-dashboard');
    }

    public function mount(){
        $this->society =Society::all();
        $this->usersCount=User::where('role_id','!=',1)->count();
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
        });
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
            
        });
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
            
        });
        $this->rejectedVerificationCount=$this->rejectedVerification->count();
        $this->issueCertificateCount=100;
    }

    public function updatedSelectedOption($value)
    {
        $this->pendingVerificationStatusCount = SocietyDetail::where('society_id',$value)->get()
            ->filter(function ($item) {
            $json = json_decode($item->status, true);
            if (!isset($json['tasks'])) return false;
            $tasks = collect($json['tasks']);
            $verification = $tasks->firstWhere('name', 'Verification');
            return ($verification && $verification['Status'] === 'Pending');
        })->count();

        $this->approvedVerificationStatusCount = SocietyDetail::where('society_id',$value)->get()
            ->filter(function ($item) {
            $json = json_decode($item->status, true);
            if (!isset($json['tasks'])) return false;
            $tasks = collect($json['tasks']);
            $verification = $tasks->firstWhere('name', 'Verification');
            return ($verification && $verification['Status'] === 'Approved');
            
        })->count();

        $this->rejectedVerificationStatusCount = SocietyDetail::where('society_id',$value)->get()
            ->filter(function ($item) {
            $json = json_decode($item->status, true);
            if (!isset($json['tasks'])) return false;
            $tasks = collect($json['tasks']);
            $verification = $tasks->firstWhere('name', 'Verification');
            return ($verification && $verification['Status'] === 'Rejected');
        })->count();
        
    }

    public function redirectToSocietyDetail($status)
    {
        return redirect()->route('admin.view-societies',['societyStatus'=>$status]);
    }

    public function redirectToCreateSociety()
    {
        return redirect()->route('menus.create_society');
    }

    public function redirectToCreateApartment()
    {
        return redirect()->route('menus.create_apartment');
    }

    public function redirectToMarkRole()
    {
        return redirect()->route('menus.mark_role');
    }
}
