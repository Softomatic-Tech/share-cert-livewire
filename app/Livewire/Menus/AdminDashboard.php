<?php

namespace App\Livewire\Menus;

use App\Models\User;
use App\Models\Role;
use App\Models\Society;
use App\Models\SocietyDetail;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
class AdminDashboard extends Component
{
    use WithPagination;
    public $societies;
    public $adminRole,$userRole;
    public $pendingApplication,$pendingApplicationCount,$pendingVerification,$pendingVerificationCount,$rejectedVerification,$rejectedVerificationCount;
    public $pendingVerificationStatus,$approvedVerificationStatus,$rejectedVerificationStatus;
    public $pendingVerificationStatusCount=0;
    public $approvedVerificationStatusCount=0;
    public $rejectedVerificationStatusCount=0;
    public $issueCertificateCount,$usersCount;
    public $selectedOption;
    public $documentName,$title,$detailId;
    public $societyDetail = null;
    public $selectedSocietyId, $societyName,$filterId;
    public string $filterKey = 'all';
    public function render()
    {
        return view('livewire.menus.admin-dashboard');
    }

    public function mount(){
        $this->societies =Society::with(['state','city'])->get();
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

    public function selectSociety($id)
    {
        $this->selectedSocietyId = $id;
        $this->societyName=Society::where('id',$this->selectedSocietyId)->value('society_name');
    }

    public function redirectToCreateSociety()
    {
        return redirect()->route('menus.create_society');
    }

    public function redirectToCreateApartment()
    {
        return redirect()->route('menus.create_apartment');
    }

    public function markRoleByAdmin()
    {
        return redirect()->route('menus.mark_role');
    }

    public function setFilter($id,$key)
    {
        $this->filterId=$id;
        $this->filterKey = $key;
    }
}
