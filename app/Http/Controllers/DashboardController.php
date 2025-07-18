<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
class DashboardController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $dashboardComponent = match ($user->role->role) {
            'Super Admin' => 'menus.superadmin-dashboard',
            'Admin' => 'menus.admin-dashboard',
            'Society User' => 'menus.user-dashboard',
            default => abort(403, 'Unauthorized'),
        };

        return view('dashboard', compact('dashboardComponent'));
    }
}
