<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ByeLawCase;
use App\Models\SocietyDetail;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    // public function mount($id)
    // {

    //     $this->societyDetails = SocietyDetail::with('society')->where('id', $id)->first();
    // }

    public function appendix()
    {
        return view('livewire.menus.appendix-pdf');
    }

    public function appendixTwo($byelaws_id)
    {
        $byelaws = ByeLawCase::findOrFail($byelaws_id);;
        $apartmentId=$byelaws->society_detail_id;
        $apartment = SocietyDetail::with('society')->findOrFail($apartmentId);
        $pdf = Pdf::loadView('livewire.pdf.appendix-two', [
            'apartment' => $apartment,
            'society'   => $apartment->society,
            'byelaws'      => $byelaws,
        ])->setPaper('A4');

        return $pdf->stream('appendix-2.pdf');
    }

    public function appendixThree($byelaws_id)
    {
        $byelaws = ByeLawCase::findOrFail($byelaws_id);;
        $apartmentId=$byelaws->society_detail_id;
        $apartment = SocietyDetail::with('society')->findOrFail($apartmentId);
        $pdf = Pdf::loadView('livewire.pdf.appendix-three', [
            'apartment' => $apartment,
            'society'   => $apartment->society,
            'byelaws'      => $byelaws,
        ])->setPaper('A4');

        return $pdf->stream('appendix-3.pdf');
    }

    public function appendixFifteen($byelaws_id)
    {
        $byelaws = ByeLawCase::findOrFail($byelaws_id);;
        $apartmentId=$byelaws->society_detail_id;
        $apartment = SocietyDetail::with('society')->findOrFail($apartmentId);
        $pdf = Pdf::loadView('livewire.pdf.appendix-fifteen', [
            'apartment' => $apartment,
            'society'   => $apartment->society,
            'byelaws'      => $byelaws,
        ])->setPaper('A4');

        return $pdf->stream('appendix-15.pdf');
    }

    public function appendixSixteen($byelaws_id)
    {
        $byelaws = ByeLawCase::findOrFail($byelaws_id);;
        $apartmentId=$byelaws->society_detail_id;
        $apartment = SocietyDetail::with('society')->findOrFail($apartmentId);
        $pdf = Pdf::loadView('livewire.pdf.appendix-sixteen', [
            'apartment' => $apartment,
            'society'   => $apartment->society,
            'byelaws'      => $byelaws,
        ])->setPaper('A4');

        return $pdf->stream('appendix-16.pdf');
    }

    public function appendixNineteen($byelaws_id)
    {
        $byelaws = ByeLawCase::findOrFail($byelaws_id);;
        $apartmentId=$byelaws->society_detail_id;
        $apartment = SocietyDetail::with('society')->findOrFail($apartmentId);
        $pdf = Pdf::loadView('livewire.pdf.appendix-nineteen', [
            'apartment' => $apartment,
            'society'   => $apartment->society,
            'byelaws'      => $byelaws,
        ])->setPaper('A4');

        return $pdf->stream('appendix-19.pdf');
    }

    public function appendixTwentyPartOne($byelaws_id)
    {
        $byelaws = ByeLawCase::findOrFail($byelaws_id);;
        $apartmentId=$byelaws->society_detail_id;
        $apartment = SocietyDetail::with('society')->findOrFail($apartmentId);
        $pdf = Pdf::loadView('livewire.pdf.appendix-twenty-part-one', [
            'apartment' => $apartment,
            'society'   => $apartment->society,
            'byelaws'      => $byelaws,
        ])->setPaper('A4');

        return $pdf->stream('appendix-20-part-one.pdf');
    }

    public function appendixTwentyPartTwo($byelaws_id)
    {
        $byelaws = ByeLawCase::findOrFail($byelaws_id);;
        $apartmentId=$byelaws->society_detail_id;
        $apartment = SocietyDetail::with('society')->findOrFail($apartmentId);
        $pdf = Pdf::loadView('livewire.pdf.appendix-twenty-part-two', [
            'apartment' => $apartment,
            'society'   => $apartment->society,
            'byelaws'      => $byelaws,
        ])->setPaper('A4');

        return $pdf->stream('appendix-20-part-two.pdf');
    }

    public function appendixTwentyOne($byelaws_id)
    {
        $byelaws = ByeLawCase::findOrFail($byelaws_id);;
        $apartmentId=$byelaws->society_detail_id;
        $apartment = SocietyDetail::with('society')->findOrFail($apartmentId);
        $pdf = Pdf::loadView('livewire.pdf.appendix-twenty-one', [
            'apartment' => $apartment,
            'society'   => $apartment->society,
            'byelaws'      => $byelaws,
        ])->setPaper('A4');

        return $pdf->stream('appendix-21.pdf');
    }
    // public function show(Request $request)
    // {
    //     $id=$request->id;
    //     $details = SocietyDetail::find($id);

    //     if (!$details) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Certificate not found'
    //         ], 404);
    //     }

    //     // Generate PDF using your existing template
    //     $pdf = Pdf::loadView('livewire.pdf.share-certificate', [
    //         'details' => $details,
    //     ])->setPaper('A4', 'landscape');

    //     $filename = 'certificate-' . $details->id . '.pdf';
    //     $path = 'temp/' . $filename;
    //     Storage::disk('public')->put($path, $pdf->output());
    //     $pdfUrl = asset('storage/' . $path);
    //     return response()->json([
    //         'success' => true,
    //         "data" => $pdfUrl
    //     ]);
    // }
}
