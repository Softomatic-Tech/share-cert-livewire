<?php

namespace App\Http\Controllers;
use App\Models\Owner;
use App\Models\User;
use App\Models\Role;
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
        return view('dashboards.admin',compact('users','adminRole','userRole'));
    }

    public function user() {
        $user_id=Auth::user()->id;
        $owners = Owner::with(['apartment','apartment.society'])->where('user_id',$user_id)->get()->groupBy('apartment_detail_id');
        $societyOwnerCounts = Owner::select('apartment_detail_id')->selectRaw('count(*) as total')->where('user_id',$user_id)->groupBy('apartment_detail_id')->pluck('total', 'apartment_detail_id');
    
        return view('dashboards.user',compact('owners','societyOwnerCounts'));
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
