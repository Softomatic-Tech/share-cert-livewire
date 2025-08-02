<?php

namespace App\Livewire\Menus;
use App\Models\Society;
use App\Models\SocietyDetail;
use Livewire\Component;

class ViewAllSocieties extends Component
{
    public $societies=[];
    public $societyStatus;
    public $societyDetail;
    public function render()
    {
        return view('livewire.menus.view-all-societies');
    }

    public function mount($societyStatus)
    {
        $this->societyStatus=$societyStatus;
        $this->societyDetail = SocietyDetail::get()
            ->filter(function ($item) {
            $json = json_decode($item->status, true);
            if (!isset($json['tasks'])) return false;
            $tasks = collect($json['tasks']);
            $verify = $tasks->firstWhere('name', 'Verify Details');
            $application = $tasks->firstWhere('name', 'Application');
            $verification = $tasks->firstWhere('name', 'Verification');
            if ($this->societyStatus == 1) {
                return (
                    ($verify && $verify['Status'] === 'Pending') &&
                    ($application && $application['Status'] === 'Pending') &&
                    ($verification && $verification['Status'] === 'Pending')
                );
            }
            if ($this->societyStatus == 2) {
                return (
                    $verify && $verify['Status'] === 'Applied' &&
                    $application && $application['Status'] === 'Applied' &&
                    $verification && $verification['Status'] === 'Pending'
                );
            }
            if ($this->societyStatus == 3) {
                return (
                    $verify && $verify['Status'] === 'Pending' &&
                    $application && $application['Status'] === 'Pending' &&
                    $verification && $verification['Status'] === 'Rejected'
                );
            }
            return false;
        })
        ->unique('society_id');
    }

    public function redirectToCreateSociety()
    {
        return redirect()->route('menus.create_society');
    }

    public function redirectToApartment($id,$societyStatus)
    {
        return redirect()->route('admin.view-apartments',['id'=>$id,'societyStatus'=>$societyStatus]);
    }
}
