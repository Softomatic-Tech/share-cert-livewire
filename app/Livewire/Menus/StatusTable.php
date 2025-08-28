<?php

namespace App\Livewire\Menus;

use Livewire\Component;
use App\Models\Society;
use App\Models\SocietyDetail;

class StatusTable extends Component
{
    public $societyId,$statusType; 
    public $societyDetails; 
    public function render()
    {
        return view('livewire.menus.status-table');
    }

    public function mount($societyId, $statusType)
    {
        $this->statusType = $statusType;
        $this->societyDetails = SocietyDetail::where('society_id',$societyId)->get()
            ->filter(function ($item) {
            $json = json_decode($item->status, true);
            if (!isset($json['tasks'])) return false;
            $tasks = collect($json['tasks']);
            $verification = $tasks->firstWhere('name', 'Verification');
            return ($verification && $verification['Status'] === $this->statusType);
        });
    }

    // public function updatedSelectedOption($value)
    // {
    //     $this->pendingVerificationStatus = SocietyDetail::where('society_id',$value)->get()
    //         ->filter(function ($item) {
    //         $json = json_decode($item->status, true);
    //         if (!isset($json['tasks'])) return false;
    //         $tasks = collect($json['tasks']);
    //         $verification = $tasks->firstWhere('name', 'Verification');
    //         return ($verification && $verification['Status'] === 'Pending');
    //     });
    //     $this->pendingVerificationStatusCount=$this->pendingVerificationStatus->count();

    //     $this->approvedVerificationStatus = SocietyDetail::where('society_id',$value)->get()
    //         ->filter(function ($item) {
    //         $json = json_decode($item->status, true);
    //         if (!isset($json['tasks'])) return false;
    //         $tasks = collect($json['tasks']);
    //         $verification = $tasks->firstWhere('name', 'Verification');
    //         return ($verification && $verification['Status'] === 'Approved');
            
    //     });
    //     $this->approvedVerificationStatusCount=$this->approvedVerificationStatus->count();

    //     $this->rejectedVerificationStatus = SocietyDetail::where('society_id',$value)->get()
    //         ->filter(function ($item) {
    //         $json = json_decode($item->status, true);
    //         if (!isset($json['tasks'])) return false;
    //         $tasks = collect($json['tasks']);
    //         $verification = $tasks->firstWhere('name', 'Verification');
    //         return ($verification && $verification['Status'] === 'Rejected');
            
    //     });
    //     $this->rejectedVerificationStatusCount=$this->rejectedVerificationStatus->count();
        
    // }
}
