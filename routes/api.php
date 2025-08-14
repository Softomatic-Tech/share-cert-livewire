<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocietyController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/societies', [SocietyController::class, 'index']);
    Route::post('/update-society', [SocietyController::class, 'updateSociety']);
    Route::post('/update-status', [SocietyController::class, 'updateStatus']);
});

