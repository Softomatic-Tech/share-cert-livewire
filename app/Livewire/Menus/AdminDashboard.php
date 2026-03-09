<?php

namespace App\Livewire\Menus;

use App\Models\User;
use App\Models\Role;
use App\Models\Society;
use App\Models\SocietyDetail;
use App\Models\Timeline;
use Livewire\Component;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On; 

class AdminDashboard extends Component
{
    use WithPagination;
    public $societies, $societyDetails = [];
    public $apartments = [];
    public $userRole;
    public $filterCounts = [];
    public $selectedSocietyId, $societyName, $filterId, $societyById;
    public $societyId = 0;
    public $filterKey = 0;
    public $search = '';
    public $timelines;
    public $pendingVerificationTimelineId = 0;
    public $pendingApplicationTimelineId = 0;
    public $showAssignModal = false;
    public $step = 1;
    public $no_of_shares, $share_value, $individual_no_of_share, $share_capital_amount;
    public $assignType = null;

    public function render()
    {
        $user=Auth::user();
        $societiesQuery = Society::with(['state', 'city']);

        if ($this->search) {
            $societiesQuery->where('society_name', 'like', '%' . $this->search . '%');
        }

        $this->societies = $societiesQuery->where('admin_id',$user->id)->get()->map(function($society) {
            $society->changes_required_count = SocietyDetail::where('society_id', $society->id)
                ->where('certificate_status', 'changes_required')
                ->count();
            return $society;
        });

        return view('livewire.menus.admin-dashboard');
    }

    public function mount()
    {
        $this->timelines = Timeline::where('id', '!=', 1)->get();
        // Store the Pending Verification timeline id for default filter
        $verificationTimeline = Timeline::where('name', 'like', '%Verification%')->first();
        $this->pendingVerificationTimelineId = $verificationTimeline ? $verificationTimeline->id : 0;
        
        $applicationTimeline = Timeline::where('name', 'like', '%Application%')->first();
        $this->pendingApplicationTimelineId = $applicationTimeline ? $applicationTimeline->id : 0;
        
        $this->userRole = Role::where('role', 'Society User')->value('id');
        // Select first assigned society by default
        $firstSociety = Society::where('admin_id', Auth::id())->first();
        if ($firstSociety) {
            $this->selectSociety($firstSociety->id);
        }
    }

    public function selectSociety($societyId)
    {
        $this->selectedSocietyId = $societyId;
        $this->societyById = Society::with(['state', 'city','admin'])->find($societyId);
        $this->societyName = $this->societyById->society_name;

        // Default to Pending Verification filter
        $this->filterKey = $this->pendingVerificationTimelineId;
        $this->filterId = $societyId;

        $this->calculateFilterCounts($societyId);
    }

    public function refreshCounts()
    {
        if ($this->selectedSocietyId) {
            $this->calculateFilterCounts($this->selectedSocietyId);
        }
    }

    public function calculateFilterCounts($societyId)
    {
        $details = SocietyDetail::where('society_id', $societyId)->get();
        $this->filterCounts = [];

        // Fetch timelines for dependency logic (matching SocietyStepper)
        $timelines = Timeline::where('id', '!=', 1)->orderBy('id')->get();
        $dependencies = [];
        $previousSteps = [];
        $idToName = [];
        foreach ($timelines as $timeline) {
            $idToName[$timeline->id] = $timeline->name;
            $dependencies[$timeline->name] = $previousSteps;
            $previousSteps[] = $timeline->name;
        }

        // Count for "All" (Pending any task)
        $this->filterCounts[0] = $details->filter(function ($item) {
            $json = json_decode($item->status, true);
            if (!isset($json['tasks'])) return false;
            return collect($json['tasks'])->contains(fn($t) => ($t['Status'] ?? null) === 'Pending');
        })->count();

        // Count for "Pending"
        $this->filterCounts['pendingCertificateStatus'] = $details->where('certificate_status', 'pending')->filter(function ($item) {
            $json = json_decode($item->status, true);
            if (!isset($json['tasks'])) {
                return false;
            }
            $tasks = collect($json['tasks']);
            return $tasks->every(function ($task) {
                if (($task['name'] ?? '') === 'Certificate Delivered') {
                    return ($task['Status'] ?? '') === 'Pending';
                }
                return ($task['Status'] ?? '') === 'Approved';
            });
        })->count();

        // Count for "Changes Required"
        $this->filterCounts['changedCertificateStatus'] = $details->where('certificate_status', 'changes_required')->count();

        // Count for each timeline
        foreach ($timelines as $timeline) {
            $currentStep = $timeline->name;
            $this->filterCounts[$timeline->id] = $details->filter(function ($item) use ($currentStep, $dependencies) {
                
                // NEW: Specialized logic for "Certificate Delivered"
                if ($currentStep === 'Certificate Delivered') {
                    if ($item->certificate_status !== 'approved') {
                        return false;
                    }
                }

                $json = json_decode($item->status, true);
                if (!isset($json['tasks'])) return false;
                $tasks = collect($json['tasks'])->keyBy('name');

                // Dependency check (matching SocietyStepper)
                foreach ($dependencies[$currentStep] ?? [] as $dep) {
                    if (($tasks[$dep]['Status'] ?? null) !== 'Approved') {
                        return false;
                    }
                }
                return ($tasks[$currentStep]['Status'] ?? null) === 'Pending';
            })->count();
        }
    }

    public function redirectToCreateSociety()
    {
        return redirect()->route('menus.create_society');
    }

    public function redirectToCreateApartment()
    {
        return redirect()->route('menus.create_apartment');
    }

    public function markRoleByAdmin()
    {
        return redirect()->route('menus.mark_role');
    }

    public function setFilter($societyId, $key)
    {
        $this->filterId = $societyId;
        $this->filterKey = $key;
    }

    public function assignShareToApartment($societyId)
    {
        $this->selectedSocietyId = $societyId;
        $this->societyById = Society::find($societyId);
        $this->no_of_shares = $this->societyById->no_of_shares;
        $this->share_value = $this->societyById->share_value;
        // if ($this->societyById && $this->societyById->no_of_shares && $this->societyById->share_value) {
        //     $this->step = 2; // skip to next form
        // } else {
        //     $this->step = 1; // show initial form
        // }
        $this->showAssignModal = true;
    }

    public function saveShares()
    {
        $this->validate([
            'no_of_shares' => 'required|numeric|min:1',
            'share_value' => 'required|numeric|min:1',
        ]);

        $society = Society::find($this->selectedSocietyId);

        if (!$society) {
            $this->dispatch('show-error', message: 'Society not found.');
            return;
        }

        // Check if shares already exist
        if (empty($society->no_of_shares) && empty($society->share_value)) {
            $message = 'Shares assigned successfully!';
        } else {
            $message = 'Shares updated successfully!';
        }

        $society->update([
            'no_of_shares' => $this->no_of_shares,
            'share_value' => $this->share_value,
        ]);

        $this->dispatch('show-success', message: $message);

        $this->showAssignModal = false;
    }

    // public function updatedAssignType($value)
    // {
    //     if ($value === 'individual') {
    //         $this->apartments = SocietyDetail::where('society_id', $this->selectedSocietyId)
    //             ->get()
    //             ->map(fn($a) => [
    //                 'id' => $a->id,
    //                 'name' => $a->building_name,
    //                 'individual_no_of_share' => '',
    //                 'share_capital_amount' => '',
    //             ])
    //             ->toArray();
    //     } else {
    //         $this->apartments = [];
    //     }
    // }

    // public function saveEqualShares()
    // {
    //     $this->validate([
    //         'individual_no_of_share' => 'required|numeric|min:1',
    //         'share_capital_amount' => 'required|numeric|min:1',
    //     ]);

    //     $society = Society::find($this->selectedSocietyId);

    //     if (!$society) {
    //         $this->dispatch('show-error', message: 'Society not found.');
    //         return;
    //     }
    //     $totalApartments = SocietyDetail::where('society_id', $this->selectedSocietyId)->count();
    //     $totalAssignedShares = $this->individual_no_of_share * $totalApartments;
    //     $totalAssignedCapital = $this->share_capital_amount * $totalApartments;
    //     $expectedTotalShares = $society->no_of_shares;
    //     $expectedTotalCapital = $society->no_of_shares * $society->share_value;
    //     if ($totalAssignedShares != $expectedTotalShares) {
    //         $this->dispatch('show-error', message: "Total assigned shares ($totalAssignedShares) do not match society's total shares ($expectedTotalShares).");
    //         $this->showAssignModal = false;
    //         return;
    //     }

    //     if ($totalAssignedCapital != $expectedTotalCapital) {
    //         $this->dispatch('show-error', message: "Total share capital ($totalAssignedCapital) does not match expected capital ($expectedTotalCapital).");
    //         $this->showAssignModal = false;
    //         return;
    //     }
    //     $response = false;
    //     $response = SocietyDetail::where('society_id', $this->selectedSocietyId)->update([
    //         'no_of_shares' => $this->individual_no_of_share,
    //         'share_capital_amount' => $this->share_capital_amount,
    //     ]);

    //     if ($response !== false) {
    //         $this->dispatch('show-success', message: 'Shares assigned equally to all apartments!');
    //         $this->reset(['individual_no_of_share', 'share_capital_amount']);
    //         $this->showAssignModal = false;
    //     } else {
    //         $this->dispatch('show-error', message: 'Some error occurs while assign shares equally to all apartments!');
    //         $this->showAssignModal = false;
    //     }
    // }

    // public function saveIndividualShares()
    // {
    //     $rules = [
    //         'apartments.*.individual_no_of_share' => 'required|numeric|min:1',
    //         'apartments.*.share_capital_amount' => 'required|numeric|min:1',
    //     ];

    //     $messages = [
    //         'required' => 'The :attribute field is required.',
    //         'numeric' => 'The :attribute must be a number.',
    //         'min' => 'The :attribute must be at least :min.',
    //     ];

    //     $attributes = [];
    //     foreach ($this->apartments as $index => $apt) {
    //         $attributes["apartments.$index.individual_no_of_share"] = "Shares for {$apt['name']}";
    //         $attributes["apartments.$index.share_capital_amount"] = "Share Amount for {$apt['name']}";
    //     }

    //     $this->validate($rules, $messages, $attributes);
    //     $totalIndividualShares = collect($this->apartments)->sum('individual_no_of_share');
    //     $totalShareCapital = collect($this->apartments)->sum('share_capital_amount');
    //     $society = Society::find($this->selectedSocietyId); // or however you link it
    //     if (!$society) {
    //         $this->dispatch('show-error', message: 'Society record not found.');
    //         return;
    //     }
    //     $expectedShareCapital = $society->no_of_shares * $society->share_value;
    //     if ($totalIndividualShares != $society->no_of_shares) {
    //         $this->dispatch('show-error', message: "Total individual shares ($totalIndividualShares) do not match society's total shares ({$society->no_of_shares}).");
    //         $this->showAssignModal = false;
    //         return;
    //     }

    //     if ($totalShareCapital != $expectedShareCapital) {
    //         $this->dispatch('show-error', message: "Total share capital ({$totalShareCapital}) does not match society's expected capital ({$expectedShareCapital}).");
    //         $this->showAssignModal = false;
    //         return;
    //     }
    //     $response = false;
    //     foreach ($this->apartments as $apartment) {
    //         $response = SocietyDetail::where('id', $apartment['id'])->update([
    //             'no_of_shares' => $apartment['individual_no_of_share'],
    //             'share_capital_amount' => $apartment['share_capital_amount'],
    //         ]);
    //     }

    //     if ($response !== false) {
    //         $this->dispatch('show-success', message: 'Individual shares assigned to all apartments successfully!');
    //         $this->showAssignModal = false;
    //         $this->reset(['apartments']);
    //         $this->resetErrorBag();
    //     } else {
    //         $this->dispatch('show-error', message: 'Some error occurs while assign shares equally to all apartments!');
    //         $this->showAssignModal = false;
    //     }
    // }

    public function closeModal()
    {
        $this->reset([
            'no_of_shares',
            'share_value',
            'assignType',
            'societyDetails',
            'step',
            'societyById',
        ]);
        $this->selectedSocietyId = null;
        $this->showAssignModal = false;
    }
}
