<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\ProfileController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Public profile route
Route::get('/profile/{username}', [ProfileController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    Route::get('/my-profile', [ProfileController::class, 'myProfile']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
    
    // Analytics
    Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index']);

    // Admin routes
    Route::get('/admin/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard']);
    Route::get('/admin/users', [\App\Http\Controllers\AdminController::class, 'users']);
});
