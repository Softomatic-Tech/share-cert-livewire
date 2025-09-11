<?php

namespace App\Livewire\Menus;
use App\Models\Society;
use App\Models\SocietyDetail;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class ViewAllSocieties extends Component
{
    public $societyStatus;
    public $societyDetail;
    public $documentName,$title,$detailId;
    public $isRejecting = false;
    public $comment,$text;
    public $search = '';
    public $showDocumentModal = false;
    public $url=null;

    public function render()
    {
        return view('livewire.menus.view-all-societies');
    }

    public function mount($societyStatus)
    {
        $this->societyStatus=$societyStatus;
        $this->getSocietyDetail();
    }

    public function updatedSearch()
    {
        if (strlen($this->search) >= 2 || $this->search === '') {
            $this->getSocietyDetail();
        }
    }

    public function getSocietyDetail()
    {
        $this->societyDetail = SocietyDetail::with('society')
            ->when(strlen($this->search) >= 2, function ($query) {
                $query->whereHas('society', function ($q) {
                    $q->where('society_name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('building_name', 'like', '%' . $this->search . '%')
                ->orWhere('apartment_number', 'like', '%' . $this->search . '%');
            })
            ->get()
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
        });
    }

    public function setDocument($id)
    {
        // $this->reset(['id']); 
        $this->detailId = $id;
        $society = SocietyDetail::find($this->detailId);
        $this->text='I have verified all details and documents. I hereby complete Verification of Application';
        $this->comment=$society->comment;
        $this->dispatch('open-modal', name: 'verificationModal');
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
            $this->dispatch('show-success', message: 'Document approved successfully!');
        }else{
            $this->dispatch('show-error', message: 'Something went wrong to approve document!');
        }
        $this->mount($this->societyStatus);
        if ($this->societyDetail->isEmpty()) {
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
            $this->dispatch('show-success', message: 'Document rejected successfully!');
        }else{
            $this->dispatch('show-error', message: 'Something went wrong to reject document!');
        }
        $this->mount($this->societyStatus);
        if ($this->societyDetail->isEmpty()) {
            return redirect()->route('admin.dashboard'); 
        }
    }

    public function viewDocument($fileUrl)
    {
        $this->showDocumentModal = true;
        $this->url = $fileUrl;
    }
}
