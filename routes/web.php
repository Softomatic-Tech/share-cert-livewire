<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Menus\SocietyMultistepForm;
use App\Livewire\Menus\CreateSociety;
use App\Livewire\Menus\CreateApartment;
use App\Livewire\Menus\SocietyList;
use App\Livewire\Menus\UserList;
use App\Livewire\Menus\IssueCertificate;
use App\Livewire\Menus\SuperAdminDashboard;
use App\Livewire\Menus\AdminDashboard;
use App\Livewire\Menus\UserDashboard;
use App\Livewire\Menus\ViewAllSocieties;
use App\Livewire\Menus\ViewAllApartments;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Livewire\Menus\UpdateSocietyStatus;
use App\Livewire\Menus\markRole;
use App\Livewire\Menus\DownloadCertificate;


Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

// Super Admin Routes
Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::get('/superadmin/dashboard', SuperAdminDashboard::class)->name('superadmin.dashboard');
    Route::get('/superadmin/society-multistep-form', SocietyMultistepForm::class)->name('menus.society_multistep_form');
    Route::get('/superadmin/society-list', SocietyList::class)->name('menus.society_list');
    Route::get('/superadmin/user-list', UserList::class)->name('menus.user_list');
    Route::get('/superadmin/user-list', markRole::class)->name('menus.user_list');
});

// Admin Routes
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/admin/view-societies/{societyStatus}', ViewAllSocieties::class)->name('admin.view-societies');
    Route::get('/admin/view-apartments/{id}/{societyStatus}', ViewAllApartments::class)->name('admin.view-apartments');
    Route::get('/admin/create-society', CreateSociety::class)->name('menus.create_society');
    Route::get('/admin/create-apartment', CreateApartment::class)->name('menus.create_apartment');
    Route::get('/admin/mark-role', markRole::class)->name('menus.mark_role');
    Route::get('/admin/certificate/{id}', DownloadCertificate::class)->name('admin.certificate.view');
});

// User Routes
Route::middleware(['auth', 'role:Society User'])->group(function () {
    Route::get('/user/dashboard', UserDashboard::class)->name('user.dashboard');
    Route::get('/user/update-society-status/{apartmentId}', UpdateSocietyStatus::class)->name('menus.update_society_status');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('/issue-certificate/{id}', IssueCertificate::class)->name('menus.issue-certificate');
    Route::get('/certificate/{id}', DownloadCertificate::class)->name('menus.certificate.view');
});

require __DIR__.'/auth.php';
