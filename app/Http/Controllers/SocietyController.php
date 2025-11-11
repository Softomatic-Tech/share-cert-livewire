<?php

namespace App\Http\Controllers;
use App\Services\UserService;
use Illuminate\Http\Request;

class SocietyController extends Controller
{

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function societyDetails(Request $request)
    {
        $search = $request->search;
        $societies = $this->userService->getSocietyDetail($search);

        if ($search && $societies->count() === 1) {
            return response()->json([
                'success' => true,
                'data' => $societies->first()
            ]);
        }

        // Otherwise, return list
        return response()->json([
            'success' => true,
            'data' => $societies
        ]);
    }

    public function show($search)
    {
        // Force numeric id; return 404 if not found or not owned by this user
        $society = $this->userService->getSocietyDetail($search);

        if (!$society) {
            return response()->json([
                'success' => false,
                'message' => 'Society not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $society
        ]);
    }

    public function updateSociety(Request $request, UserService $userService)
    {
        $response = $userService->updateSocietyDetails(
            $request->all(),
            $request->society_id,
            $request->apartment_id
        );

        return response()->json($response);
    }

    public function uploadAgreementCopy(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|integer|exists:society_details,id',
            'agreementCopy' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);
        try {
            $result = $this->userService->uploadSocietyDocument($request->apartment_id, $request->file('agreementCopy'),'agreementCopy','agreementCopy');
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function uploadMemberShipForm(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|integer|exists:society_details,id',
            'memberShipForm' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);
        try {
            $result = $this->userService->uploadSocietyDocument($request->apartment_id, $request->file('memberShipForm'),'memberShipForm','memberShipForm');
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function uploadAllotmentLetter(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|integer|exists:society_details,id',
            'allotmentLetter' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);
        try {
            $result = $this->userService->uploadSocietyDocument($request->apartment_id, $request->file('allotmentLetter'),'allotmentLetter','allotmentLetter');
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function uploadPossessionLetter(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|integer|exists:society_details,id',
            'possessionLetter' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);
        try {
            $result = $this->userService->uploadSocietyDocument($request->apartment_id, $request->file('possessionLetter'),'possessionLetter','possessionLetter');
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function updateStatus(Request $request)
    {
        $response=$this->userService->updateStatus($request->apartment_id); 
        return response()->json($response);
    }

    public function isFileApproved(Request $request)
    {
        $statusData = $request->statusData;
        $fileName = $request->fileName;

        $data = [
            'statusData' => $statusData,
            'fileName' => $fileName,
        ];
        $response=$this->userService->checkFileApproval($data);
        return response()->json($response);
    }
}
