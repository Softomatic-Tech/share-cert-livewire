<?php

namespace App\Livewire\Menus;

use App\Services\UserService;
use App\Models\SocietyDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserDashboard extends Component
{
    public $societyDetail = [];
    public $apartmentList = [];
    public $search = '';
    protected $detailId,$userService,$checkApproved,$selectedApartmentId;
    public $url=null;
    public $showDocumentModal = false;

    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function render()
    {
        return view('livewire.menus.user-dashboard');
    }

    public function mount()
    {
        $this->apartmentList=$this->userService->getSocietyDetail();
    }

    public function selectApartment($id)
    {
        $this->selectedApartmentId = $id;
        $this->societyDetail=$this->userService->getSocietyDetail($this->selectedApartmentId);
    }

    public function verifyDetails($apartmentId)
    {
        return redirect()->route('menus.update_society_status',['apartmentId'=>$apartmentId]);
    }

    public function getFileStatus($statusData, $fileName)
    {
        $applicationTask = collect($statusData['tasks'])->firstWhere('name', 'Application');
        if ($applicationTask) {
            $subtask = collect($applicationTask['subtasks'] ?? [])
                ->firstWhere('fileName', basename(trim($fileName)));
            return $subtask['status'] ?? null; // could be Approved / Rejected / null
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
}
