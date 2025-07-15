<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Menus\CreateSociety;
use App\Livewire\Menus\SocietyList;
use App\Livewire\Menus\RegisterSociety;
use App\Livewire\Menus\IssueCertificate;
use App\Livewire\Menus\SuperAdminDashboard;
use App\Livewire\Menus\AdminDashboard;
use App\Livewire\Menus\UserDashboard;
use App\Livewire\Menus\ViewAllSocieties;
use App\Livewire\Menus\ViewAllApartments;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');


// Super Admin Routes
Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::get('/superadmin/dashboard', SuperAdminDashboard::class)->name('superadmin.dashboard');
});

// Admin Routes
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/admin/view-societies', ViewAllSocieties::class)->name('admin.view-societies');
    Route::get('/admin/view-apartments', ViewAllApartments::class)->name('admin.view-apartments');
});

// User Routes
Route::middleware(['auth', 'role:Society User'])->group(function () {
    Route::get('/user/dashboard', UserDashboard::class)->name('user.dashboard');
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
