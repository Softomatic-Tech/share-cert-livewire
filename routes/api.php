<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocietyController;
use App\Http\Controllers\CertificateController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/society-details', [SocietyController::class, 'societyDetails']);
    Route::post('/update-society', [SocietyController::class, 'updateSociety']);
    Route::post('/update-status', [SocietyController::class, 'updateStatus']);
    Route::post('/upload-agreement-copy', [SocietyController::class, 'uploadAgreementCopy']);
    Route::post('/upload-memberShip-form', [SocietyController::class, 'uploadMemberShipForm']);
    Route::post('/upload-allotment-letter', [SocietyController::class, 'uploadAllotmentLetter']);
    Route::post('/upload-possession-letter', [SocietyController::class, 'uploadPossessionLetter']);
    Route::post('/certificate', [CertificateController::class, 'show']);
    Route::post('/approve-certificate', [CertificateController::class, 'approveCertificate']);
    Route::post('/submit-remarks', [CertificateController::class, 'submitRemarks']);
});
