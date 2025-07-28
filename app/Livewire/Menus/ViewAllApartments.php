<?php

namespace App\Livewire\Menus;
use App\Models\SocietyDetail;
use Livewire\Component;
use Illuminate\Support\Facades\Log; 

class ViewAllApartments extends Component
{
    public $societyDetails = [];
    public $documentName,$title,$detailId;
    public $isRejecting = false;
    public $comment,$status;
    public $societyDetail;
    
    protected $listeners = ['setDocument' => 'setDocument'];
    public function render()
    {
        return view('livewire.menus.view-all-apartments');
    }

    public function mount($id,$societyStatus)
    {
        if($societyStatus==1){
            $this->status='Applied';
        }else{
            $this->status='Pending';
        }
        $this->societyDetails = SocietyDetail::where('society_id',$id)->get()
            ->filter(function ($item) {
            $json = json_decode($item->status, true);
            if (isset($json['tasks'])) {
                foreach ($json['tasks'] as $task) {
                    if ($task['name'] === 'Verify Details' && $task['Status'] === $this->status) {
                        return true;
                    }
                }
            }
            return false;
        });
    }

    public function redirectToCreateApartment()
    {
        return redirect()->route('menus.create_apartment');
    }

    public function setDocument($title,$docName,$id)
    {
        $this->reset(['documentName', 'title']); 
        $this->detailId = $id;
        $this->documentName = $docName;
        $this->title=$title;
        $this->dispatch('open-modal', name: 'documentModal');
    }

    public function setRejecting()
    {
        $this->isRejecting = true;
    }

    public function approveDocument($detailId)
    {
        $this->detailId=$detailId;
        $society = \App\Models\SocietyDetail::find($this->detailId); 
        $data = json_decode($society->status, true);
        foreach ($data['tasks'] as &$task) {
            if (in_array($task['name'], ['Verify Details', 'Application'])) {
                $task['Status'] = 'Approved';
            }
        }
        $society->status = json_encode($data);
        $society->save();
        session()->flash('success', 'Document approved successfully!');
    }

    public function rejectDocument($detailId)
    {
        log::info('reject docs');
        $this->validate([
            'comment' => 'required|string|min:3',
        ]);
        $this->detailId=$detailId;
        $society = SocietyDetail::find($this->detailId); 
        $data = json_decode($society->status, true);
        foreach ($data['tasks'] as &$task) {
            if (in_array($task['name'], ['Verify Details', 'Application'])) {
                $task['Status'] = 'Rejected';
            }
        }
        $society->status = json_encode($data);
        $society->comment = $this->comment;
        $society->save();
        session()->flash('success', 'Document rejected successfully!');
    }

}
