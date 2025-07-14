<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Menus\CreateSociety;
use App\Livewire\Menus\SocietyList;
use App\Livewire\Menus\RegisterSociety;
use App\Livewire\Menus\IssueCertificate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');
// Super Admin Routes
Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::get('/superadmin/dashboard', [DashboardController::class, 'superAdmin'])->name('superadmin.dashboard');
});

// Admin Routes
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::post('/admin/{id}/mark-role', [DashboardController::class, 'markRole'])->name('admin.markRole');
});

// User Routes
Route::middleware(['auth', 'role:Society User'])->group(function () {
    Route::get('/user/dashboard', [DashboardController::class, 'user'])->name('user.dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('/create-society', CreateSociety::class)->name('menus.create_society');
    Route::get('/society-list', SocietyList::class)->name('menus.society_list');
    Route::get('/register-society', RegisterSociety::class)->name('menus.register_society');
    Route::get('/issue-certificate/{id}', IssueCertificate::class)->name('menus.issue-certificate');
});

require __DIR__.'/auth.php';
