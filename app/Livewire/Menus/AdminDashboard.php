<?php

namespace App\Livewire\Menus;

use App\Models\User;
use App\Models\Role;
use App\Models\Society;
use App\Models\SocietyDetail;
use App\Models\Timeline;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
class AdminDashboard extends Component
{
    use WithPagination;
    public $societies,$societyDetails=[];
    public $apartments=[];
    public $userRole;
    public $pendingApplication,$pendingApplicationCount,$pendingVerification,$pendingVerificationCount,$rejectedVerification,$rejectedVerificationCount;
    public $pendingVerificationStatus,$approvedVerificationStatus,$rejectedVerificationStatus;
    public $pendingVerificationStatusCount=0;
    public $approvedVerificationStatusCount=0;
    public $rejectedVerificationStatusCount=0;
    public $issueCertificateCount,$usersCount;
    public $selectedSocietyId, $societyName,$filterId,$societyById;
    public $societyId=0;
    public $filterKey = 0;
    public $timelines;
    public $showAssignModal = false;
    public $step = 1; 
    public $no_of_shares,$share_value,$individual_no_of_share,$share_capital_amount;
    public $assignType = null;
    
    public function render()
    {
        return view('livewire.menus.admin-dashboard');
    }

    public function mount(){
        $this->timelines =Timeline::where('id', '!=', 1)->get();
        $this->societies =Society::with(['state','city'])->get();
        $this->usersCount=User::where('role_id','!=',1)->count();
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

    public function selectSociety($societyId)
    {
        $this->selectedSocietyId = $societyId;
        $this->societyName=Society::where('id',$this->selectedSocietyId)->value('society_name');
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

    public function setFilter($societyId,$key)
    {
        $this->filterId=$societyId;
        $this->filterKey = $key;
    }

    public function assignShareToApartment($societyId)
    {
        $this->selectedSocietyId = $societyId;
        $this->societyById =Society::find($societyId);
        if ($this->societyById && $this->societyById->no_of_shares && $this->societyById->share_value) {
            $this->step = 2; // skip to next form
        } else {
            $this->step = 1; // show initial form
        }
        $this->showAssignModal = true;
    }

    public function updatedAssignType($value)
    {
        if ($value === 'individual') {
            $this->apartments = SocietyDetail::where('society_id', $this->selectedSocietyId)
                ->get()
                ->map(fn($a) => [
                    'id' => $a->id,
                    'name' => $a->building_name,
                    'individual_no_of_share' => '',
                    'share_capital_amount' => '',
                ])
                ->toArray();
        }  else {
        $this->apartments = [];
        }
    }

    public function saveEqualShares()
    {
        $this->validate([
            'individual_no_of_share' => 'required|numeric|min:1',
            'share_capital_amount' => 'required|numeric|min:1',
        ]);

        $society = Society::find($this->selectedSocietyId);

        if (!$society) {
            $this->dispatch('show-error', message: 'Society not found.');
            return;
        }
        $totalApartments = SocietyDetail::where('society_id', $this->selectedSocietyId)->count();
        $totalAssignedShares = $this->individual_no_of_share * $totalApartments;
        $totalAssignedCapital = $this->share_capital_amount * $totalApartments;
        $expectedTotalShares = $society->no_of_shares;
        $expectedTotalCapital = $society->no_of_shares * $society->share_value;
        if ($totalAssignedShares != $expectedTotalShares) {
            $this->dispatch('show-error', message: "Total assigned shares ($totalAssignedShares) do not match society's total shares ($expectedTotalShares).");
            $this->showAssignModal = false;
            return;
        }

        if ($totalAssignedCapital != $expectedTotalCapital) {
            $this->dispatch('show-error', message: "Total share capital ($totalAssignedCapital) does not match expected capital ($expectedTotalCapital).");
            $this->showAssignModal = false;
            return;
        }
        $response = false; 
        $response = SocietyDetail::where('society_id', $this->selectedSocietyId)->update([
            'no_of_shares' => $this->individual_no_of_share,
            'share_capital_amount' => $this->share_capital_amount,
        ]);

        if ($response !== false) {
            $this->dispatch('show-success', message:  'Shares assigned equally to all apartments!');
            $this->reset(['individual_no_of_share','share_capital_amount']);
            $this->showAssignModal = false;
        } else {
            $this->dispatch('show-error', message:  'Some error occurs while assign shares equally to all apartments!');
            $this->showAssignModal = false;
        }
    }

    public function saveIndividualShares()
    {
        $rules = [
            'apartments.*.individual_no_of_share' => 'required|numeric|min:1',
            'apartments.*.share_capital_amount'   => 'required|numeric|min:1',
        ];

        $messages = [
            'required' => 'The :attribute field is required.',
            'numeric'  => 'The :attribute must be a number.',
            'min'      => 'The :attribute must be at least :min.',
        ];
    
        $attributes = [];
        foreach ($this->apartments as $index => $apt) {
            $attributes["apartments.$index.individual_no_of_share"] = "Shares for {$apt['name']}";
            $attributes["apartments.$index.share_capital_amount"]   = "Share Amount for {$apt['name']}";
        }

        $this->validate($rules, $messages, $attributes);
        $totalIndividualShares = collect($this->apartments)->sum('individual_no_of_share');
        $totalShareCapital = collect($this->apartments)->sum('share_capital_amount');
        $society = Society::find($this->selectedSocietyId); // or however you link it
        if (!$society) {
            $this->dispatch('show-error', message: 'Society record not found.');
            return;
        }
        $expectedShareCapital = $society->no_of_shares * $society->share_value;
        if ($totalIndividualShares != $society->no_of_shares) {
            $this->dispatch('show-error', message: "Total individual shares ($totalIndividualShares) do not match society's total shares ({$society->no_of_shares}).");
            $this->showAssignModal = false;
            return;
        }

        if ($totalShareCapital != $expectedShareCapital) {
            $this->dispatch('show-error', message: "Total share capital ({$totalShareCapital}) does not match society's expected capital ({$expectedShareCapital}).");
            $this->showAssignModal = false;
            return;
        }
        $response = false; 
        foreach ($this->apartments as $apartment) {
            $response = SocietyDetail::where('id', $apartment['id'])->update([
                'no_of_shares' => $apartment['individual_no_of_share'],
                'share_capital_amount' => $apartment['share_capital_amount'],
            ]);
        }

        if ($response !== false) {
            $this->dispatch('show-success', message:  'Individual shares assigned to all apartments successfully!');
            $this->showAssignModal = false;
            $this->reset(['apartments']);
            $this->resetErrorBag();
        } else {
            $this->dispatch('show-error', message:  'Some error occurs while assign shares equally to all apartments!');
            $this->showAssignModal = false;
        }
    }

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
