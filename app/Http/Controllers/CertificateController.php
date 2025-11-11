<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SocietyDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class CertificateController extends Controller
{
    public function show(Request $request)
    {
        $id=$request->id;
        $details = SocietyDetail::find($id);

        if (!$details) {
            return response()->json([
                'success' => false,
                'message' => 'Certificate not found'
            ], 404);
        }

        // Generate PDF using your existing template
        $pdf = Pdf::loadView('livewire.pdf.share-certificate', [
            'details' => $details,
        ])->setPaper('A4', 'landscape');

        $filename = 'certificate-' . $details->id . '.pdf';
        $path = 'temp/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());
        $pdfUrl = asset('storage/' . $path);
        return response()->json([
            'success' => true,
            "data" => $pdfUrl
        ]);
    }

    public function approveCertificate(Request $request)
    {
        $id=$request->id;
        $certificate_status = 'approved';
        $society = SocietyDetail::find($id);
        if ($society) {
        $updated = SocietyDetail::where('id', $id)
            ->update(['certificate_status' => $certificate_status]);
            return response()->json([
                'success' => true,
                'message' => "Thank you! The certificate has been approved"
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Society record not found.'
            ]);
        }
    }

    public function submitRemarks(Request $request)
    {
        $certificate_remark=$request->certificate_remark;
        if (empty($certificate_remark)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter remarks before submitting.'
            ]);
        }
        $society = SocietyDetail::find($request->id);
        if ($society){
            $updated = SocietyDetail::where('id', $request->id)->update(['certificate_remark' => $certificate_remark]);
            return response()->json([
                'success' => true,
                'message' => 'Remark saved and certificate marked for changes.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Society record not found.'
            ]);
        }
    }
}
