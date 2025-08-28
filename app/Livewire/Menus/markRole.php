<?php

namespace App\Livewire\Menus;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class markRole extends Component
{
    use WithPagination;
    public function render()
    {
        return view('livewire.menus.mark-role',['users'=>User::where('role_id','!=',1)->paginate(10)]);
    }

    public function markRole($userID,$roleID){
        $user = User::findOrFail($userID);
        $user->role_id = $roleID;
        $user->save();
        if($user)
            $this->dispatch('showSuccess', message: 'Role updated successfully!');
        else
            $this->dispatch('showError', message: 'Role not updated!');
    }
}
