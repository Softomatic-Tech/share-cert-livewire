<?php

namespace App\Livewire\Menus;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SocietyDetail;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class DownloadCertificate extends Component
{
    public $details;
    public $pdfUrl;
    public $detailId;
    public $showRemarkBox = false;
    public $certificate_status;
    public $certificate_remark = '';

    public function render()
    {
        return view('livewire.menus.download-certificate');
    }

    public function mount($id)
    {
        $this->loadSocietyDetails($id);
        // Generate PDF and store temporarily in storage/app/public/temp
        $pdf = Pdf::loadView('livewire.pdf.share-certificate', [
            'details' => $this->details,
        ])->setPaper('A4', 'landscape');

        $filename = 'certificate-' . $this->details->id . '.pdf';
        $path = 'temp/' . $filename;

        Storage::disk('public')->put($path, $pdf->output());
        $this->pdfUrl = asset('storage/' . $path);
    }

    public function loadSocietyDetails($id){
        $this->detailId = $id;
        $this->details = SocietyDetail::findOrFail($this->detailId);
        $this->certificate_status = $this->details->certificate_status ?? 'pending';
    }

    public function approveCertificate()
    {
        $this->certificate_status = 'approved';
        $society = SocietyDetail::find($this->detailId);
        if ($this->detailId) {
        $updated = SocietyDetail::where('id', $this->detailId)
            ->update(['certificate_status' => $this->certificate_status]);
            $this->showRemarkBox = false;
            $this->certificate_status = 'approved';
            $this->dispatch('show-success', message:  "Thank you! The certificate has been approved");
            $this->mount($this->detailId);
        } else {
            $this->dispatch('show-error', message:  "Society record not found.");
        }
    }

    public function showRemarksBox()
    {
        $this->showRemarkBox = true;
    }

    public function submitRemarks()
    {
        $this->certificate_status='changes_required';
        if (empty($this->certificate_remark)) {
            $this->dispatch('show-error', message:  "Please enter remarks before submitting.");
            return;
        }
        $society = SocietyDetail::find($this->detailId);
        if ($this->detailId) {
            $updated = SocietyDetail::where('id', $this->detailId)->update(['certificate_status' => $this->certificate_status,'certificate_remark' => $this->certificate_remark]);

            $this->showRemarkBox = false;
            $this->certificate_remark = '';
            $this->dispatch('show-success', message:  "Remark saved and certificate marked for changes.");
            $this->mount($this->detailId);
        } else {
            $this->dispatch('show-error', message:  "Society record not found.");
        }
    }

}
