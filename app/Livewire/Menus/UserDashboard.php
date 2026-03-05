<?php

namespace App\Livewire\Menus;

use App\Services\UserService;
use App\Models\SocietyDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class UserDashboard extends Component
{
    public $societyDetail = [];
    public $apartmentList = [];
    public $search = '';
    public $selectedApartmentId;
    public $detailId, $checkApproved;
    public $url=null;
    public $showDocumentModal = false;

    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function render()
    {
        $this->apartmentList = $this->userService->getSocietyDetail($this->search);
        return view('livewire.menus.user-dashboard');
    }

    public function mount()
    {
        $search ='';
        $userMobile = Auth::user()->phone;
        $apartments=$this->userService->getSocietyDetail($search,$userMobile);
        if ($apartments->isNotEmpty()) {
            $this->selectApartment($apartments->first()->id);
        }
    }

    public function selectApartment($id)
    {
        log::info('Selected Apartment ID: ' . $id);
        $this->selectedApartmentId = $id;
        $userMobile = Auth::user()->phone;
        $this->societyDetail=$this->userService->getSocietyDetail($this->selectedApartmentId,$userMobile);
    }

    public function verifyDetails($apartmentId)
    {
        return redirect()->route('menus.update_society_status',['apartmentId'=>$apartmentId]);
    }

    public function viewDetails($apartmentId)
    {
        return redirect()->route('menus.view_society_status',['apartmentId'=>$apartmentId]);
    }

    public function getFileStatus($statusData, $fileName)
    {
        $applicationTask = collect($statusData['tasks'])->firstWhere('name', 'Application');
        if ($applicationTask) {
            $subtask = collect($applicationTask['subtasks'] ?? [])
                ->firstWhere('fileName', trim((string)$fileName));
            return $subtask['status'] ?? null;
        }
        return null;
    }

    public function viewDocument($id,$fileUrl,$isApproved)
    {
        $this->detailId=$id;
        $this->showDocumentModal = true;
        $this->url = $fileUrl;
        $this->checkApproved = $isApproved;
    }

    public function updateFileStatus($detailId,$fileName,$fileStatus)
    {
        $this->detailId=$detailId;
        $society = SocietyDetail::find($this->detailId); 
        $societyData = json_decode($society->status, true);
        foreach ($societyData['tasks'] as &$task) {
            if ($task['name'] === 'Application') {
                if (!isset($task['subtasks']) || !is_array($task['subtasks'])) {
                    $task['subtasks'] = [];
                }

                $index = collect($task['subtasks'])->search(function ($sub) use ($fileName) {
                    return trim((string) $sub['fileName']) === trim((string) $fileName);
                });

                if ($index !== false) {
                    $task['subtasks'][$index]['status'] = $fileStatus;
                } else {
                    $task['subtasks'][] = [
                        "fileName" => trim((string) $fileName),
                        "status"   => $fileStatus];
                }
            }
        }

        $society->status = json_encode($societyData);
        $society->save();
        if($society){
            $this->dispatch('show-success', message: 'Document '.$fileStatus.' successfully!');
            $this->selectApartment($this->selectedApartmentId);
            $this->showDocumentModal = false;
        }else{
            $this->dispatch('show-error', message: 'Something went wrong to approve document!');
        }
    }
}
