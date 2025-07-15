<?php

namespace App\Livewire\Menus;

use App\Models\Society;
use App\Models\SocietyDetail;
use Livewire\Component;

class UserDashboard extends Component
{
     public $selectedSociety  = null; 
    public $selectedBuilding = null;
    public $societies = [];
    public $buildings = [];
    public $taskStatus  = [];

    public function render()
    {
        return view('livewire.menus.user-dashboard');
    }

    public function mount()
    {
        $this->societies =Society::all();
    }

    public function updatedSelectedSociety($societyId)
    {
        $this->buildings = SocietyDetail::where('society_id', $societyId)->select('id','building_name')->distinct()->get();
        $this->selectedBuilding = null;
    }
    public function updatedselectedBuilding($apartmentId)
    {
        $this->fetchTaskStatus($apartmentId);
    }


    public function fetchTaskStatus($apartmentId)
    {
            $jsonData=
            '{"apartmentID": "J-101",
            "tasks": [
                {
                "name": "Verify Details",
                "responsibilityOf": "ApartmentOwner",
                "Status": "Pending",
                "createdBy": "System",
                "createDateTime": "23-06-2025-11:30:55PM",
                "updatedBy": "Shivangi",
                "updateDateTime": "23-06-2025-11:30:55PM"
                },
                {
                "name": "Apply",
                "responsibilityOf": "ApartmentOwner",
                "Status": "Pending",
                "createdBy": "Shivangi",
                "createDateTime": "23-06-2025-11:30:55PM",
                "updatedBy": "Shivangi",
                "updateDateTime": "23-06-2025-11:30:55PM"
                },
                {
                "name": "Verification",
                "Status": "Pending",
                "createdBy": "System",
                "createDateTime": "23-06-2025-11:30:55PM",
                "updatedBy": "Shivangi",
                "updateDateTime": "23-06-2025-11:30:55PM",
                "subtask": [
                    {
                    "name": "Upload Doc1 Again",
                    "Status": "Pending",
                    "createdBy": "Shivangi",
                    "createDateTime": "23-06-2025-11:30:55PM",
                    "updatedBy": "Shivangi",
                    "updateDateTime": "23-06-2025-11:30:55PM"
                    },
                    {
                    "name": "Change Name To Marathi",
                    "Status": "Pending",
                    "createdBy": "Shivangi",
                    "createDateTime": "23-06-2025-11:30:55PM",
                    "updatedBy": "Shivangi",
                    "updateDateTime": "23-06-2025-11:30:55PM"
                    }
                ]
                },
                {
                "name": "Certificate Generated",
                "responsibilityOf": "DearSociety",
                "Status": "Pending",
                "createdBy": "Shivangi",
                "createDateTime": "23-06-2025-11:30:55PM",
                "updatedBy": "Shivangi",
                "updateDateTime": "23-06-2025-11:30:55PM"
                },
                {
                "name": "Certificate Delivered",
                "responsibilityOf": "DearSociety",
                "Status": "Pending",
                "createdBy": "Shivangi",
                "createDateTime": "23-06-2025-11:30:55PM",
                "updatedBy": "Shivangi",
                "updateDateTime": "23-06-2025-11:30:55PM"
                }
            ]
            }';
            $this->taskStatus  = json_decode($jsonData, true);
    }
}
