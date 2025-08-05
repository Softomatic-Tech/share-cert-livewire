<?php
namespace App\Livewire\Menus;
use App\Models\Society;
use App\Models\SocietyDetail;
use Livewire\Component;
use \Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
class SocietyList extends Component
{
    use WithFileUploads;
    public $openAccordionId = null;
    public $uploadDocId=null;
    public $docType=null;
    public $document;
    public $search = '';

    public function render()
    {
        $societies = Society::query()
            ->when(strlen($this->search) >= 2, function ($query) {
                $query->where('society_name', 'like', '%' . $this->search . '%');
            })
            ->get();
        return view('livewire.menus.society-list',compact('societies'));
    }

    public function toggleAccordion($id)
    {
        $this->openAccordionId = $this->openAccordionId === $id ? null : $id;
    }

}
