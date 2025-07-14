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

    public function render()
    {
        $societies = Society::with('details')->orderBy('id','desc')->get();
        return view('livewire.menus.society-list',compact('societies'));
    }

    public function toggleAccordion($id)
    {
        $this->openAccordionId = $this->openAccordionId === $id ? null : $id;
    }

    public function verification($id){
        $detail = SocietyDetail::find($id);

        if ($detail) {
            $detail->status = 'verified';
            $detail->save();
            $this->openAccordionId = $detail->society_id;
        }

    }

    public function openUploadModal($id, $type)
    {
        $this->uploadDocId = $id;
        $this->docType = $type;
    }

    public function closeModal()
    {
        $this->reset(['uploadDocId', 'docType', 'document']);
    }

    protected function rules()
    {
        return [
            'document' => 'required|file|mimes:pdf,jpeg,png,jpg|max:2048',
        ];
    }

    public function uploadDocument()
    {
        log::info('Uploading document', [
            'uploadDocId' => $this->uploadDocId,
            'docType' => $this->docType,
            'document' => $this->document ? $this->document->getClientOriginalName() : null,
        ]);
        $this->validate([
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf,xls,xlsx,csv|max:10240', // 10MB
        ]);

        $filename = time() . '_' . $this->document->getClientOriginalName();
        $path = $this->document->storeAs('society_docs', $filename, 'public');
        log::info('Document stored at', ['path' => $path]);
        exit;
        $detail = SocietyDetail::find($this->uploadDocId);
        if ($detail) {
            $detail->{$this->docType} = $filename;
            log::info('docType ', $detail->{$this->docType});
            $detail->{$this->docType . '_status'} = null; // reset status
            $detail->save();
            session()->flash('success', 'Document uploaded successfully!');
        }

        $this->reset(['document', 'uploadDocId', 'docType']);
    }
}
