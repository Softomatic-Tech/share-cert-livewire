<?php

namespace App\Livewire\Menus;

use App\Models\User;
use App\Models\Role;
use App\Models\Society;
use App\Models\SocietyDetail;
use Livewire\Component;
use Illuminate\Http\RedirectResponse;

class AdminDashboard extends Component
{
    public $users;
    public $adminRole;
    public $userRole;
    public $societyCount;
    public $societyDetailsCount,$pendingSociety,$pendingSocietyCount,$rejectedSociety,$rejectedSocietyCount;
    public $issueCertificateCount;

    public function render()
    {
        return view('livewire.menus.admin-dashboard');
    }

    public function mount(){
        $this->users=User::orderBy('id','desc')->get();
        $this->adminRole=Role::where('role','Admin')->value('id');
        $this->userRole=Role::where('role','Society User')->value('id');

        $this->pendingSociety = SocietyDetail::get()
            ->filter(function ($item) {
            $json = json_decode($item->status, true);
            if (isset($json['tasks'])) {
                foreach ($json['tasks'] as $task) {
                    if ($task['name'] === 'Verify Details' && $task['Status'] === 'Applied') {
                        return true;
                    }
                }
            }
            return false;
        })
        ->unique('society_id');
        $this->pendingSocietyCount=$this->pendingSociety->count();

    $this->rejectedSociety = SocietyDetail::get()
        ->filter(function ($item) {
        $json = json_decode($item->status, true);
        if (isset($json['tasks'])) {
            foreach ($json['tasks'] as $task) {
                if ($task['name'] === 'Verify Details' && $task['Status'] === 'Pending') {
                    return true;
                }
            }
        }
        return false;
    })
    ->unique('society_id');
    $this->rejectedSocietyCount=$this->rejectedSociety->count();

    $this->issueCertificateCount=100;
    }
    
    public function markRole($userID,$roleID){
        $user = User::findOrFail($userID);
        $user->role_id = $roleID;
        $user->save();
        if($user)
            session()->flash('success', 'Role updated successfully!');
        else
            session()->flash('error', 'Role not updated!');
    }

    public function redirectToSociety($status)
    {
        return redirect()->route('admin.view-societies',['societyStatus'=>$status]);
    }

    public function redirectToApartmentPage()
    {
        return redirect()->route('admin.view-apartments');
    }
}
