<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Role;
use App\Models\Society;
use App\Models\SocietyDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function superAdmin() {
        return view('dashboards.superadmin');
    }

    public function admin() {
        $users=User::orderBy('id','desc')->paginate(10);
        $adminRole=Role::where('role','Admin')->value('id');
        $userRole=Role::where('role','Society User')->value('id');
        $societyCount = Society::count();
        $societyDetailsCount = SocietyDetail::count();
        $issueCertificateCount=100;
        return view('dashboards.admin',compact('users','adminRole','userRole','societyCount','societyDetailsCount','issueCertificateCount'));
    }

    public function user() {
        $user_id=Auth::user()->id;
        // $societies = Society::whereHas('details', function ($query) use ($user_id) {
        //     $query->where('user_id', $user_id);
        // })
        // ->with(['details' => function ($query) use ($user_id) {
        //     $query->where('user_id', $user_id);
        // }])
        // ->get()
        // ->map(function ($society) {
        //     $firstDetail = $society->details->first();
        
        //     $totalOwners = $society->details->map(function ($detail) {
        //         $count = 0;
        //         if (!empty($detail->owner1_name) && !empty($detail->owner1_mobile)) $count++;
        //         if (!empty($detail->owner2_name) && !empty($detail->owner2_mobile)) $count++;
        //         if (!empty($detail->owner3_name) && !empty($detail->owner3_mobile)) $count++;
        //         return $count;
        //     })->sum();
    
        //     return [
        //         'society_id'=>$society->id,
        //         'society_name'   => $society->society_name,
        //         'building_name'  => $firstDetail->building_name ?? '',
        //         'apartment_number'   => $firstDetail->apartment_number ?? '',
        //         'owner_name' => $firstDetail->owner1_name,
        //         'owner_mobile' => $firstDetail->owner1_mobile,
        //         'total_owners'   => $totalOwners,
        //     ];

        //     $society->details = $society->details->map(function($detail) {
        //         $ownerCount = 0;
        //         if (!empty($detail->owner1_name)) $ownerCount++;
        //         if (!empty($detail->owner2_name)) $ownerCount++;
        //         if (!empty($detail->owner3_name)) $ownerCount++;
        //         $detail->total_owners = $ownerCount;
        
        //         return $detail;
        //     });
        //     return $society;
        //});
        $details = SocietyDetail::with('society')
        ->where('user_id', Auth::id())
        ->select('id', 'society_id', 'building_name', 'apartment_number', 'owner1_name', 'owner1_mobile', 'owner2_name', 'owner2_mobile','owner3_name', 'owner3_mobile','status')
        ->get()
        ->map(function ($item) {
            $ownerCount = 0;
            if (!empty($item->owner1_name)) $ownerCount++;
            if (!empty($item->owner2_name)) $ownerCount++;
            if (!empty($item->owner3_name)) $ownerCount++;
            $item->owner_count = $ownerCount;
            return $item;
        });
        return view('dashboards.user',compact('details'));
    }

    public function markRole(Request $request, $id){
        $user = User::findOrFail($id);
        $user->role_id = $request->input('role_id');
        $user->save();
        if($user)
        return redirect()->back()->with('success', 'Role updated successfully.');
        else
        return redirect()->back()->with('error', 'Role not updated.');
    }
}
