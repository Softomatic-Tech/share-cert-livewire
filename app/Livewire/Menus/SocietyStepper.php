<?php

namespace App\Livewire\Menus;

use Livewire\Component;
use App\Models\Timeline;
use App\Models\Society;
use App\Models\SocietyDetail;

class SocietyStepper extends Component
{
    public $detailId, $societyId, $societyKey, $societyDetails;
    public $comment, $text, $checkApproved, $timelines, $timelineValues;
    public $isRejecting = false;
    public $verificationModal = false;
    public $apartment_id, $building_name, $apartment_number, $certificate_no, $owner1_name, $owner1_mobile, $owner1_email, $owner2_name, $owner2_mobile, $owner2_email, $owner3_name, $owner3_mobile, $owner3_email;
    public $did_you_purchase_the_apartment_before_the_society_was_registered = 'No';
    public $did_you_sign_at_the_time_of_the_society_registration = 'No';
    public $did_the_previous_owner_sign_the_registration_documents = 'No';
    public $has_the_flat_transfer_related_fee_been_paid_to_the_society = 'No';
    public $have_physical_documents_been_submitted_to_the_society = 'No';
    public $is_society_signed_member_available = 'No';
    public $showDocumentModal = false;
    public $editOwnersModal = false;
    public $url = null;
    public $taskNameMap = [];
    public $timelineMap = [];
    public function render()
    {
        return view('livewire.menus.society-stepper');
    }

    public function mount($id, $key)
    {
        // Fetch timelines
        $this->timelines = Timeline::where('id', '!=', 1)->orderBy('id')->get();
        $this->timelineMap = $this->timelines->pluck('name', 'id')->toArray();
        $this->timelineValues = array_values($this->timelineMap);
        $this->taskNameMap = [];
        foreach ($this->timelines as $timeline) {
            $this->taskNameMap[$timeline->name] = $timeline->name;
        }
        $this->loadSocietyDetails($id, $key);
    }

    public function loadSocietyDetails($id, $key)
    {
        $this->societyId = $id;
        $this->societyKey = $key;
        $this->societyDetails = SocietyDetail::with('society')
            ->where('society_id', $this->societyId)->get()
            ->filter(function ($item) {

                if ($this->societyKey == 'changes_required') {
                    return $item->certificate_status == 'changes_required';
                }

                $json = json_decode($item->status, true);
                if (!isset($json['tasks'])) return false;
                $tasks = collect($json['tasks'])->keyBy('name');

                if ($this->societyKey == 'certificate_pending') {
                    if ($item->certificate_status != 'pending') {
                        return false;
                    }
                    $taskStatuses = collect($tasks)->pluck('Status')->values();
                    if ($taskStatuses->last() != 'Pending') {
                        return false;
                    }
                    return $taskStatuses->slice(0, -1)->every(fn($s) => $s === 'Approved');
                }

                if ($this->societyKey == 0) {
                    return $tasks->contains(fn($task) => ($task['Status'] ?? null) === 'Pending');
                }
                $this->timelines = Timeline::where('id', '!=', 1)->orderBy('id')->get();
                $dependencies = [];
                $previousSteps = [];
                $idToName = [];

                foreach ($this->timelines as $timeline) {
                    $idToName[$timeline->id] = $timeline->name;
                    $dependencies[$timeline->name] = $previousSteps;
                    $previousSteps[] = $timeline->name;
                }
                $currentStep = $idToName[$this->societyKey] ?? null;
                if (!$currentStep) return false;

                // NEW: Specialized logic for "Certificate Delivered"
                if ($currentStep === 'Certificate Delivered') {
                    if ($item->certificate_status !== 'approved') {
                        return false;
                    }
                }

                // Ensure all dependencies are Approved
                foreach ($dependencies[$currentStep] ?? [] as $dep) {
                    if (($tasks[$dep]['Status'] ?? null) !== 'Approved') {
                        return false;
                    }
                }
                // Finally, check the selected filter is Pending
                return ($tasks[$currentStep]['Status'] ?? null) === 'Pending';
            });
    }

    public $fileType = null;

    public function getFileStatus($statusData, $fileName, $fileType = null)
    {
        $applicationTask = collect($statusData['tasks'])->firstWhere('name', $this->timelineValues[0]);
        if ($applicationTask) {
            $subtasks = collect($applicationTask['subtasks'] ?? []);

            // Search by fileType first
            if ($fileType) {
                $subtask = $subtasks->firstWhere('fileType', $fileType);
                if ($subtask) return $subtask['status'] ?? null;
            }

            // Fallback to fileName
            $subtask = $subtasks->firstWhere('fileName', basename(trim($fileName)));
            return $subtask['status'] ?? null;
        }
        return null;
    }

    public function areAllFilesApproved($statusData, array $expectedFiles = [])
    {
        $applicationTask = collect($statusData['tasks'])->firstWhere('name', $this->timelineValues[0]);
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
        $this->detailId = $id;
        $apartment = SocietyDetail::findOrFail($this->detailId);
        if ($apartment) {
            $this->apartment_id = $this->detailId;
            $this->building_name = $apartment->building_name;
            $this->apartment_number = $apartment->apartment_number;
            $this->certificate_no = $apartment->certificate_no;
            $this->owner1_name = $apartment->owner1_name;
            $this->owner1_mobile = $apartment->owner1_mobile;
            $this->owner1_email = $apartment->owner1_email;
            $this->owner2_name = $apartment->owner2_name;
            $this->owner2_mobile = $apartment->owner2_mobile;
            $this->owner2_email = $apartment->owner2_email;
            $this->owner3_name = $apartment->owner3_name;
            $this->owner3_mobile = $apartment->owner3_mobile;
            $this->owner3_email = $apartment->owner3_email;
            $this->did_you_purchase_the_apartment_before_the_society_was_registered = $apartment->did_you_purchase_the_apartment_before_the_society_was_registered ?? 'No';
            $this->did_you_sign_at_the_time_of_the_society_registration = $apartment->did_you_sign_at_the_time_of_the_society_registration ?? 'No';
            $this->did_the_previous_owner_sign_the_registration_documents = $apartment->did_the_previous_owner_sign_the_registration_documents ?? 'No';
            $this->has_the_flat_transfer_related_fee_been_paid_to_the_society = $apartment->has_the_flat_transfer_related_fee_been_paid_to_the_society ?? 'No';
            $this->have_physical_documents_been_submitted_to_the_society = $apartment->have_physical_documents_been_submitted_to_the_society ?? 'No';

            $society = Society::find($this->societyId);
            $this->is_society_signed_member_available = $society ? $society->is_list_of_signed_member_available : 'No';
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
            'did_you_purchase_the_apartment_before_the_society_was_registered' => 'required|in:Yes,No',
            'did_you_sign_at_the_time_of_the_society_registration' => 'required|in:Yes,No',
            'did_the_previous_owner_sign_the_registration_documents' => 'required|in:Yes,No',
            'has_the_flat_transfer_related_fee_been_paid_to_the_society' => 'required|in:Yes,No',
            'have_physical_documents_been_submitted_to_the_society' => 'required|in:Yes,No',
        ]);

        $apartment = SocietyDetail::findOrFail($this->apartment_id);
        if (!$apartment) {
            return [
                'status' => false,
                'message' => 'Owner Details not found!'
            ];
        }
        $society = Society::find($this->societyId);
        $response = $apartment->update([
            'building_name'     => $this->building_name,
            'apartment_number'  => $this->apartment_number,
            'certificate_no' => $this->certificate_no,
            'owner1_name'       => $this->owner1_name,
            'owner1_mobile'     => $this->owner1_mobile,
            'owner1_email'      => $this->owner1_email ?? null,
            'owner2_name'       => $this->owner2_name ?? null,
            'owner2_mobile'     => $this->owner2_mobile ?? null,
            'owner2_email'      => $this->owner2_email ?? null,
            'owner3_name'       => $this->owner3_name ?? null,
            'owner3_mobile'     => $this->owner3_mobile ?? null,
            'owner3_email'      => $this->owner3_email ?? null,
            'did_you_purchase_the_apartment_before_the_society_was_registered' => $this->did_you_purchase_the_apartment_before_the_society_was_registered,
            'did_you_sign_at_the_time_of_the_society_registration' => $this->did_you_sign_at_the_time_of_the_society_registration,
            'did_the_previous_owner_sign_the_registration_documents' => $this->did_the_previous_owner_sign_the_registration_documents,
            'has_the_flat_transfer_related_fee_been_paid_to_the_society' => $this->has_the_flat_transfer_related_fee_been_paid_to_the_society,
            'have_physical_documents_been_submitted_to_the_society' => $this->have_physical_documents_been_submitted_to_the_society,
        ]);

        if ($response) {
            $this->dispatch('show-success', message: 'Owner Details updated successfully!');
            $this->fetchOwnersDetail($this->apartment_id);
            $this->editOwnersModal = false;
        } else {
            $this->dispatch('show-error', message: 'Some error occurs while update owner details');
            $this->editOwnersModal = false;
        }
    }

    public function viewDocument($id, $fileUrl, $isApproved)
    {
        $this->detailId = $id;
        $this->showDocumentModal = true;
        $this->url = $fileUrl;
        $this->checkApproved = $isApproved;
    }

    public function setDocument($id)
    {
        $this->detailId = $id;
        $society = SocietyDetail::find($this->detailId);
        $this->text = 'I have verified all details and documents. I hereby complete Verification of Application';
        $this->comment = $society->comment;
        $this->verificationModal = true;
    }

    public function setRejecting()
    {
        $this->isRejecting = true;
    }

    public function approveDetail($detailId)
    {
        $this->detailId = $detailId;
        $society = SocietyDetail::find($this->detailId);
        $data = json_decode($society->status, true);
        foreach ($data['tasks'] as &$task) {
            if ($task['name'] == $this->timelineValues[1]) {
                $task['Status'] = 'Approved';
            }
        }
        $society->status = json_encode($data);
        $society->save();
        if ($society) {
            $this->dispatch('show-success', message: 'Document with society details have been approved successfully!');
            $this->dispatch('status-updated');
            $this->mount($this->societyId, $this->societyKey);
        } else {
            $this->dispatch('show-error', message: 'Something went wrong to approve document!');
        }
    }

    public function rejectDetail($detailId)
    {
        $this->validate([
            'comment' => 'required|string|min:3',
        ]);
        $this->detailId = $detailId;
        $society = SocietyDetail::find($this->detailId);
        $data = json_decode($society->status, true);
        foreach ($data['tasks'] as &$task) {
            if ($task['name'] == $this->timelineValues[1]) {
                $task['Status'] = 'Rejected';
            }

            if ($task['name'] == 'Verify Details' || $task['name'] == $this->timelineValues[0]) {
                $task['Status'] = 'Pending';
            }
        }
        $society->status = json_encode($data);
        $society->comment = $this->comment;
        $society->save();
        if ($society) {
            $this->dispatch('show-success', message: 'Document rejected successfully!');
            $this->dispatch('status-updated');
            $this->mount($this->societyId, $this->societyKey);
        } else {
            $this->dispatch('show-error', message: 'Something went wrong to reject document!');
        }
    }

    public function updateFileStatus($detailId, $fileName, $fileStatus)
    {
        $this->detailId = $detailId;
        $society = SocietyDetail::find($this->detailId);
        $societyData = json_decode($society->status, true);
        foreach ($societyData['tasks'] as &$task) {
            if ($task['name'] === $this->timelineValues[0]) {
                // Ensure subtasks array exists
                if (!isset($task['subtasks']) || !is_array($task['subtasks'])) {
                    $task['subtasks'] = [];
                }

                // Look for existing subtask
                $index = collect($task['subtasks'])->search(function ($sub) use ($fileName) {
                    return trim((string) $sub['fileName']) === trim((string) $fileName);
                });

                if ($index !== false) {
                    // Update existing entry (Approved ↔ Rejected)
                    $task['subtasks'][$index]['status'] = $fileStatus;
                } else {
                    // Insert new entry
                    $task['subtasks'][] = [
                        "fileName" => trim((string) $fileName),
                        "status"   => $fileStatus
                    ];
                }
            }
        }

        $society->status = json_encode($societyData);
        $society->save();
        if ($society) {
            $this->dispatch('show-success', message: 'Document ' . $fileStatus . ' successfully!');
            $this->dispatch('status-updated');
            $this->mount($this->societyId, $this->societyKey);
            $this->showDocumentModal = false;
        } else {
            $this->dispatch('show-error', message: 'Something went wrong to approve document!');
        }
    }

    public function generateCertificate($id)
    {
        $this->detailId = $id;
        $society = SocietyDetail::find($this->detailId);
        if (!$society->no_of_shares) {
            $this->dispatch('show-error', message: 'Individual no of shares is empty. Please update to generate certificate!');
            return;
        }
        $data = json_decode($society->status, true);
        foreach ($data['tasks'] as &$task) {
            if ($task['name'] == $this->timelineValues[2]) {
                $task['Status'] = 'Approved';
            }
        }
        $society->status = json_encode($data);
        $society->save();
        if ($society) {
            $this->dispatch('show-success', message: 'All details have verified and certificate have been generated successfully!');
            $this->dispatch('status-updated');
            $this->mount($this->societyId, $this->societyKey);
        } else {
            $this->dispatch('show-error', message: 'Something went wrong to approve document!');
        }
    }
}
