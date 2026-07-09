<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccessCardController;
use App\Http\Controllers\DoorLockController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('/auth/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'resetPassword']);

// Public profile route
Route::get('/profile/{username}', [ProfileController::class, 'show']);

// Lock hardware verification (authenticated via secret_key in payload)
Route::post('/locks/verify', [DoorLockController::class, 'verify']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    Route::get('/my-profile', [ProfileController::class, 'myProfile']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
    
    // Analytics
    Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index']);

    // Access Cards (user-facing)
    Route::prefix('access-cards')->group(function () {
        Route::get('/', [AccessCardController::class, 'index']);
        Route::get('/logs', [AccessCardController::class, 'logs']);
        Route::get('/{id}', [AccessCardController::class, 'show']);
        Route::post('/{id}/toggle', [AccessCardController::class, 'toggleActivation']);
        Route::post('/{id}/tuya-unlock', [AccessCardController::class, 'tuyaUnlock']);
    });

    // Admin routes
    Route::get('/admin/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard']);
    Route::get('/admin/users', [\App\Http\Controllers\AdminController::class, 'users']);

    // Admin — Door Locks & Access Cards
    Route::get('/admin/door-locks', [DoorLockController::class, 'index']);
    Route::post('/admin/door-locks', [DoorLockController::class, 'store']);
    Route::put('/admin/door-locks/{id}', [DoorLockController::class, 'update']);
    Route::post('/admin/access-cards/assign', [DoorLockController::class, 'assignCard']);
    Route::delete('/admin/access-cards/{id}', [DoorLockController::class, 'revokeCard']);
    Route::get('/admin/access-logs', [DoorLockController::class, 'allLogs']);
    Route::get('/admin/door-locks/users', [DoorLockController::class, 'listUsers']);
});

