<?php
namespace App\Livewire\Menus;
use App\Models\Society;
use Livewire\Component;
use \Livewire\WithPagination;
class SocietyList extends Component
{
    use WithPagination;
    public $perPage=5;

    public function render()
    {
        return view('livewire.menus.society-list',['society'=>Society::paginate($this->perPage)]);
    }
}
