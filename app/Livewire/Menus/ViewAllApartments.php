<?php

namespace App\Livewire\Menus;
use App\Models\SocietyDetail;
use Livewire\Component;
use Illuminate\Support\Facades\Log; 

class ViewAllApartments extends Component
{
    public $societyDetails;
    public $documentName,$title,$detailId;
    public $isRejecting = false;
    public $comment,$societyStatus;
    public $societyDetail;
    
    protected $listeners = ['setDocument' => 'setDocument'];
    public function render()
    {
        return view('livewire.menus.view-all-apartments');
    }

    public function mount($id,$societyStatus)
    {
        $this->societyStatus=$societyStatus;
        $this->societyDetails = SocietyDetail::where('society_id',$id)->get()
            ->filter(function ($item) {
            $json = json_decode($item->status, true);
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
        });
    }

    public function redirectToCreateApartment()
    {
        return redirect()->route('menus.create_apartment');
    }

    public function setDocument($id)
    {
        // $this->reset(['id']); 
        $this->detailId = $id;
        $society = SocietyDetail::find($this->detailId);
        $this->comment=$society->comment;
        $this->dispatch('open-modal', name: 'documentModal');
    }

    public function setRejecting()
    {
        $this->isRejecting = true;
    }

    public function approveDocument($detailId)
    {
        $this->detailId=$detailId;
        $society = SocietyDetail::find($this->detailId); 
        $data = json_decode($society->status, true);
        foreach ($data['tasks'] as &$task) {
            if ($task['name']=='Verification') {
                $task['Status'] = 'Approved';
            }
        }
        $society->status = json_encode($data);
        $society->save();
        if($society){
            $this->dispatch('showSuccess', message: 'Document approved successfully!');
        }else{
            $this->dispatch('showError', message: 'Something went wrong to approve document!');
        }
        $this->mount($society->society_id,$this->societyStatus);
        if ($this->societyDetails->isEmpty()) {
            return redirect()->route('admin.dashboard'); 
        }
    }

    public function rejectDocument($detailId)
    {
        $this->validate([
            'comment' => 'required|string|min:3',
        ]);
        $this->detailId=$detailId;
        $society = SocietyDetail::find($this->detailId); 
        $data = json_decode($society->status, true);
        foreach ($data['tasks'] as &$task) {
            if ($task['name']=='Verification') {
                $task['Status'] = 'Rejected';
            }

            if ($task['name']=='Verify Details' || $task['name']=='Application') {
                $task['Status'] = 'Pending';
            }
        }
        $society->status = json_encode($data);
        $society->comment = $this->comment;
        $society->save();
        if($society){
            $this->dispatch('showSuccess', message: 'Document rejected successfully!');
        }else{
            $this->dispatch('showError', message: 'Something went wrong to reject document!');
        }
        $this->mount($society->society_id,$this->societyStatus);
        if ($this->societyDetails->isEmpty()) {
            return redirect()->route('admin.dashboard'); 
        }
    }

}
