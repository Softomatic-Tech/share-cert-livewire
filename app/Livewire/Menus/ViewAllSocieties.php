<?php

namespace App\Livewire\Menus;
use App\Models\Society;
use App\Models\SocietyDetail;
use Livewire\Component;

class ViewAllSocieties extends Component
{
    public $societies=[];
    public $societyStatus,$status;
    public $societyDetail;
    public function render()
    {
        return view('livewire.menus.view-all-societies');
    }

    public function mount($societyStatus)
    {
        $this->$societyStatus=$societyStatus;
        if($societyStatus==1){
            $this->status='Applied';
        }else{
            $this->status='Pending';
        }
        
        $this->societyDetail = SocietyDetail::get()
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
