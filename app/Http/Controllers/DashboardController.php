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
        $owners = Owner::with(['society', 'apartment'])->where('user_id',$user_id)->get() ->groupBy('society_id');
        $societyOwnerCounts = Owner::select('society_id')->selectRaw('count(*) as total')->where('user_id',$user_id)->groupBy('society_id')->pluck('total', 'society_id');
        return view('dashboards.user',compact('owners','societyOwnerCounts'));
    }
}
