<?php

namespace App\Http\Controllers;
use App\Models\Owner;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function superAdmin() {
        return view('dashboards.superadmin');
    }

    public function admin() {
        return view('dashboards.admin');
    }

    public function user() {
        $user_id=Auth::user()->id;
        $owners = Owner::with(['apartment','apartment.society'])->where('user_id',$user_id)->get()->groupBy('apartment_detail_id');
        $societyOwnerCounts = Owner::select('apartment_detail_id')->selectRaw('count(*) as total')->where('user_id',$user_id)->groupBy('apartment_detail_id')->pluck('total', 'apartment_detail_id');
    
        return view('dashboards.user',compact('owners','societyOwnerCounts'));
    }
}
