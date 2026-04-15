<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use App\Models\SocietyDetail;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $user = $request->user();
        $userMobile = $request->mobile ?? ($user->mobile_no ?? $user->phone);
        $society_id = $request->society_id;
        $unit_no = $request->unit_no;
        log::info('societyDetails function');
        log::info('unit_no=' . $unit_no . ' society_id=' . $society_id);
        $societies = $this->userService->getSocietyDetail($search, $userMobile);

        if ($search && $societies->count() === 1) {

            return response()->json([
                'success' => true,
                'data' => $societies->first()
            ]);
        }
        if ($societies->count() === 0) {
            $prefix = DB::connection('mysql_second')->table('table_prefix')->where('society_id', $society_id)->value('prefix');
            $member_table = $prefix . 'members_detail';
            $membership_table = $prefix . 'membership_detail';
            $society = DB::connection('mysql_second')->table('society')->where('id', $society_id)->first();
            $mebershipID = DB::connection('mysql_second')->table($membership_table)->where('unit_no', $unit_no)->value('id');
            $societies = DB::connection('mysql_second')->table($member_table)->where('membership_id', $mebershipID)->get();


            $data = $societies[0]->owners_detail;
            $data = json_decode($data, true);


            $societies[0]->owner1_name = isset($data[1])
                ? trim($data[1]['first_name'] . ' ' . $data[1]['middle_name'] . ' ' . $data[1]['last_name'])
                : '';

            $societies[0]->owner2_name = isset($data[2])
                ? trim($data[2]['first_name'] . ' ' . $data[2]['middle_name'] . ' ' . $data[2]['last_name'])
                : '';
            $societies[0]->owner1_mobile = $societies[0]->contact_no1;
            $societies[0]->owner1_email = $societies[0]->email_id1;
            $societies[0]->owner2_mobile = $societies[0]->contact_no2;
            $societies[0]->owner2_email = $societies[0]->email_id2;




            $societies[0]->society = $society;
        }
        // Otherwise, return list
        return response()->json([
            'success' => true,
            'data' => $societies
        ]);
    }


    public function societyDetailsById($id,  Request $request)
    {
        $mobile_no = $request->query('mobile_no');
        $societies = $this->userService->getSocietyDetail($search = "", $mobile_no);
        $count = sizeof($societies);
        log::info('count=' . $count);
        if ($count > 0) {
            $societies = SocietyDetail::with(['society.state', 'society.city'])->find($id);
        } else {
            $society_id = DB::connection('mysql_second')->table('users')->where('mobile_no', $mobile_no)->value('society_id');
            $prefix = DB::connection('mysql_second')->table('table_prefix')->where('society_id', $society_id)->value('prefix');
            $member_table = $prefix . 'members_detail';
            $membership_table = $prefix . 'membership_detail';
            $society = DB::connection('mysql_second')->table('society')->select('id AS society_id', 'society_name', 'address As address_1', 'total_unit AS total_flats', 'reg_no As registration_no')->where('id', $society_id)->first();

            $societies = DB::connection('mysql_second')->table($member_table)->where('id', $id)->first();


            $data = $societies->owners_detail;
            $data = json_decode($data, true);


            $societies->owner1_name = isset($data[1])
                ? trim($data[1]['first_name'] . ' ' . $data[1]['middle_name'] . ' ' . $data[1]['last_name'])
                : '';

            $societies->owner2_name = isset($data[2])
                ? trim($data[2]['first_name'] . ' ' . $data[2]['middle_name'] . ' ' . $data[2]['last_name'])
                : '';
            $societies->owner1_mobile = $societies->contact_no1;
            $societies->owner1_email = $societies->email_id1;
            $societies->owner2_mobile = $societies->contact_no2;
            $societies->owner2_email = $societies->email_id2;
            $societies->apartment_id = $id;



            $societies->society = $society;
        }


        if (!$societies) {
            return response()->json([
                'success' => false,
                'message' => 'Society not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $societies
        ]);
    }

    public function show(Request $request)
    {
        // Force numeric id; return 404 if not found or not owned by this user
        $search = $request->search;
        $userMobile = $request->mobile;
        $society = $this->userService->getSocietyDetail($search, $userMobile);

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
        log::info('updateSociety call');
        log::info($request->all());
        $response = $userService->updateSocietyDetails(
            $request->all(),
            $request->society_id,
            $request->apartment_id
        );

        $fileFields = [
            'agreementCopy',
            'memberShipForm',
            'allotmentLetter',
            'possessionLetter',
            'stampDutyProof',
            'transferorSignature',
            'deathCertificate',
            'nominationRecord',
            'successionCertificate',
            'allotmentMembershipLetter',
        ];

        $uploadedFiles = [];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $uploadResult = $userService->uploadSocietyDocument(
                    $request->apartment_id,
                    $request->file($field),
                    $field
                );

                if (!empty($uploadResult['status'])) {
                    $uploadedFiles[] = $field;
                }
            }
        }
        log::info('response=' . $response);
        if (!$response['status']) {
            return response()->json($response, $response['code'] ?? 400);
        }
        if ($response['status'] || !empty($uploadedFiles)) {
            // update task status
            $statusResponse = $userService->updateStatus($request->apartment_id, $request->user_id);

            $message = $response['message'] ?? 'Society details updated.';
            if (!empty($uploadedFiles)) {
                $message .= ' Files updated: ' . implode(', ', $uploadedFiles) . '.';
            }
            if (!empty($statusResponse['message'])) {
                $message .= ' ' . $statusResponse['message'];
            }
            return response()->json([
                'status' => true,
                'message' => $message
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => $response['message'] ?? 'Update failed'
        ]);
    }

    public function uploadAgreementCopy(Request $request)
    {
        log::info('uploadAgreementCopy call');
        $request->validate([
            'apartment_id' => 'required|integer|exists:society_details,id',
            'agreementCopy' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);
        try {
            $result = $this->userService->uploadSocietyDocument($request->apartment_id, $request->file('agreementCopy'), 'agreementCopy', 'agreementCopy');
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
        log::info('uploadMemberShipForm call');
        $request->validate([
            'apartment_id' => 'required|integer|exists:society_details,id',
            'memberShipForm' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);
        try {
            $result = $this->userService->uploadSocietyDocument($request->apartment_id, $request->file('memberShipForm'), 'memberShipForm', 'memberShipForm');
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
        log::info('uploadAllotmentLetter call');
        $request->validate([
            'apartment_id' => 'required|integer|exists:society_details,id',
            'allotmentLetter' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);
        try {
            $result = $this->userService->uploadSocietyDocument($request->apartment_id, $request->file('allotmentLetter'), 'allotmentLetter', 'allotmentLetter');
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
        log::info('uploadPossessionLetter call');
        $request->validate([
            'apartment_id' => 'required|integer|exists:society_details,id',
            'possessionLetter' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);
        try {
            $result = $this->userService->uploadSocietyDocument($request->apartment_id, $request->file('possessionLetter'), 'possessionLetter', 'possessionLetter');
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
        log::info('updateStatus call');
        $user_id = $request->user_id;
        $response = $this->userService->updateStatus($request->apartment_id, $user_id);
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
        $response = $this->userService->checkFileApproval($data);
        return response()->json($response);
    }
    public function memberDetails(Request $request)
    {
        log::info('memberDetails function call');
        $request->validate([
            'mobile_no' => 'required',
            'society_id' => 'required',
        ]);
        try {
            log::info(12345);
            $mobile_no = $request->mobile_no;
            $society_id = $request->society_id;
            $prefix = DB::connection('mysql_second')->table('table_prefix')->where('society_id', $society_id)->value('prefix');
            $member_table = $prefix . 'members_detail';
            $membership_table = $prefix . 'membership_detail';

            $societies = DB::connection('mysql_second')->table($member_table)->where('contact_no1', $mobile_no)->get(['id', 'membership_id']);
            foreach ($societies as $row) {
                $row->unit_no = DB::connection('mysql_second')->table($membership_table)->where('id', $row->membership_id)->value('unit_no');
            }
            return response()->json([
                'success' => true,
                'data' => $societies
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function states(Request $request)
    {

        try {
            $states = State::all();
            return response()->json([
                'success' => true,
                'data' => $states
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function cities(Request $request)
    {

        try {

            $cities = City::all();
            return response()->json([
                'success' => true,
                'data' => $cities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
