<?php
namespace App\Livewire\Menus;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class UserList extends Component
{
    use WithPagination;
    public function render()
    {
        return view('livewire.menus.user-list',['users'=>User::paginate(10)]);
    }
}
