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
    public $documentName,$title,$detailId;
    public $isRejecting = false;
    public $comment,$text,$checkApproved;
    public $societyDetail = null;
    public $showDocumentModal = false;
    public $editOwnersModal= false;
    public $url=null;
    public $apartment_id,$building_name, $apartment_number, $owner1_name, $owner1_mobile ,$owner1_email ,$owner2_name, $owner2_mobile ,$owner2_email ,$owner3_name, $owner3_mobile ,$owner3_email;
    public $selectedSocietyId;
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

    public function selectSociety($id)
    {
        $this->selectedSocietyId = $id;
        $this->societyDetail = SocietyDetail::with('society')
            ->where('society_id',$id)->get()
            ->filter(function ($item) {
            $json = json_decode($item->status, true);
            if (!isset($json['tasks'])) return false;
            $tasks = collect($json['tasks']);
            $verification = $tasks->firstWhere('name', 'Verification');
            return (
                $verification && $verification['Status'] === 'Pending'
            );
        });
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

    public function approveDetail($detailId)
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

    public function rejectDetail($detailId)
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

    public function updateFileStatus($detailId,$fileName,$fileStatus)
    {
        $this->detailId=$detailId;
        $society = SocietyDetail::find($this->detailId); 
        $societyData = json_decode($society->status, true);
        foreach ($societyData['tasks'] as &$task) {
            if ($task['name']=='Application') {
                // Check if subtask already exists
                $found = false;
                foreach ($task['subtasks'] ?? [] as &$subtask) {
                    if (trim((string) $subtask['fileName']) === trim((string) $fileName)) {
                        // Update existing status
                        $subtask['status'] = $fileStatus;
                        $found = true;
                        break;
                    }
                }

                // If not found, insert new subtask
                if (! $found) {
                    // Add new subtask only if not already added
                    $task['subtasks'][] = [
                        "fileName" => trim((string) $fileName),
                        "status"   => $fileStatus
                    ];
                }
            }
        }
        $society->status = json_encode($societyData);
        $society->save();
        if($society){
            $this->dispatch('show-success', message: 'Document '.$fileStatus.' successfully!');
        }else{
            $this->dispatch('show-error', message: 'Something went wrong to approve document!');
        }
    }

    public function viewDocument($id,$fileUrl,$isApproved)
    {
        $this->detailId=$id;
        $this->showDocumentModal = true;
        $this->url = $fileUrl;
        $this->checkApproved = $isApproved;
    }

    public function fetchOwnersDetail($id)
    {
        $this->detailId=$id;
        $apartment = SocietyDetail::findOrFail($this->detailId);
        if ($apartment) {
            $this->apartment_id = $this->detailId;
            $this->building_name = $apartment->building_name;
            $this->apartment_number = $apartment->apartment_number;
            $this->owner1_name = $apartment->owner1_name;
            $this->owner1_mobile = $apartment->owner1_mobile;
            $this->owner1_email = $apartment->owner1_email;
            $this->owner2_name = $apartment->owner2_name;
            $this->owner2_mobile = $apartment->owner2_mobile;
            $this->owner2_email = $apartment->owner2_email;
            $this->owner3_name = $apartment->owner3_name;
            $this->owner3_mobile = $apartment->owner3_mobile;
            $this->owner3_email = $apartment->owner3_email;
        }
        $this->editOwnersModal = true;
    }

    public function updateOwnersDetail()
    {
        $this->validate([
            'building_name' => 'required|string|max:255',
            'apartment_number' => 'required|string|max:50',
            'owner1_name' => 'required|string|max:255',
            'owner1_email' => 'nullable|string|email|max:255',
            'owner1_mobile' => 'required|digits:10',
            'owner2_name' => 'nullable|string|max:255',
            'owner2_email' => 'nullable|string|email|max:255',
            'owner2_mobile' => 'nullable|digits:10',
            'owner3_name' => 'nullable|string|max:255',
            'owner3_email' => 'nullable|string|email|max:255',
            'owner3_mobile' => 'nullable|digits:10',
        ]);

        $apartment = SocietyDetail::findOrFail($this->apartment_id);
        if (!$apartment) {
            return [
                'status' => false,
                'message' => 'Owner Details not found!'
            ];
        }
        $response=$apartment->update([
            'building_name'     => $this->building_name,
            'apartment_number'  => $this->apartment_number,
            'owner1_name'       => $this->owner1_name,
            'owner1_mobile'     => $this->owner1_mobile,
            'owner1_email'      => $this->owner1_email ?? null,
            'owner2_name'       => $this->owner2_name ?? null,
            'owner2_mobile'     => $this->owner2_mobile ?? null,
            'owner2_email'      => $this->owner2_email ?? null,
            'owner3_name'       => $this->owner3_name ?? null,
            'owner3_mobile'     => $this->owner3_mobile ?? null,
            'owner3_email'      => $this->owner3_email ?? null
        ]);

        if ($response) {
            $this->dispatch('show-success', message:  'Owner Details updated successfully!');
            $this->editOwnersModal = false;
            $this->fetchOwnersDetail($this->apartment_id);
        } else {
            $this->dispatch('show-error', message:  'Some error occurs while update owner details');
            $this->editOwnersModal = false;
        }
    }

    public function markRoleByAdmin()
    {
        return redirect()->route('menus.mark_role');
    }
}
