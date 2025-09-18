<?php

namespace App\Livewire\Menus;

use Livewire\Component;
use App\Models\Society;
use App\Models\SocietyDetail;
use Illuminate\Support\Facades\Log;

class SocietyStepper extends Component
{
    public $detailId,$societyId, $societyKey,$societyDetails; 
    public $comment,$text,$checkApproved;
    public $isRejecting = false;
    public $verificationModal = false;
    public $apartment_id,$building_name, $apartment_number, $owner1_name, $owner1_mobile ,$owner1_email ,$owner2_name, $owner2_mobile ,$owner2_email ,$owner3_name, $owner3_mobile ,$owner3_email;
    public $showDocumentModal = false;
    public $editOwnersModal= false;
    public $url=null;
    public function render()
    {
        return view('livewire.menus.society-stepper');
    }

    public function mount($id,$key)
    {
        $this->loadSocietyDetails($id,$key);
    }

    public function loadSocietyDetails($id,$key)
    {
        $this->societyId = $id;
        $this->societyKey = $key;
        $this->societyDetails = SocietyDetail::with('society')
            ->where('society_id',$this->societyId)->get()
            ->filter(function ($item) {
            $json = json_decode($item->status, true);
            if (!isset($json['tasks'])) return false;
            $tasks = collect($json['tasks']);

            if ($this->societyKey == 'all') {
                return $tasks->contains(fn($task) => ($task['Status'] ?? null) === 'Pending');
            } else {
                $data = $tasks->firstWhere('name', $this->societyKey);
                return ($data && ($data['Status'] ?? null) === 'Pending');
            }
        });
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

    
    public function areAllFourFilesApproved($statusData, array $expectedFiles = [])
    {
        $applicationTask = collect($statusData['tasks'])->firstWhere('name', 'Application');
        if (!$applicationTask) {
            return false; // No Application task found
        }
        $subtasks = collect($applicationTask['subtasks'] ?? []);
        if (empty($expectedFiles)) {
            return false;
        }
        foreach ($expectedFiles as $file) {
            $subtask = $subtasks->firstWhere('fileName', basename(trim($file)));

            if (!$subtask || ($subtask['status'] ?? null) !== 'Approved') {
                return false; // either file missing OR not approved
            }
        }
        return true; // all 4 files exist and are approved
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
            $this->fetchOwnersDetail($this->apartment_id);
            $this->editOwnersModal = false;
        } else {
            $this->dispatch('show-error', message:  'Some error occurs while update owner details');
            $this->editOwnersModal = false;
        }
    }

    public function viewDocument($id,$fileUrl,$isApproved)
    {
        $this->detailId=$id;
        $this->showDocumentModal = true;
        $this->url = $fileUrl;
        $this->checkApproved = $isApproved;
    }

    public function setDocument($id)
    {
        $this->detailId = $id;
        $society = SocietyDetail::find($this->detailId);
        $this->text='I have verified all details and documents. I hereby complete Verification of Application';
        $this->comment=$society->comment;
        $this->verificationModal=true;
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
            $this->dispatch('show-success', message: 'Document with society details have been approved successfully!');
            $this->mount($this->societyId,$this->societyKey);
        }else{
            $this->dispatch('show-error', message: 'Something went wrong to approve document!');
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
            $this->mount($this->societyId,$this->societyKey);
        }else{
            $this->dispatch('show-error', message: 'Something went wrong to reject document!');
        }
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
            $this->mount($this->societyId,$this->societyKey);
        }else{
            $this->dispatch('show-error', message: 'Something went wrong to approve document!');
        }
    }
}
