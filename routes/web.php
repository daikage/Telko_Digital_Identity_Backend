<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAdminController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/login', [WebAdminController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [WebAdminController::class, 'login']);
Route::post('/admin/logout', [WebAdminController::class, 'logout'])->name('logout');
Route::get('/admin/dashboard', [WebAdminController::class, 'dashboard'])->middleware('auth')->name('admin.dashboard');

// Door Locks & Access Cards (server-side admin)
Route::middleware('auth')->group(function () {
    Route::get('/admin/door-locks', [WebAdminController::class, 'doorLocks'])->name('admin.doorlocks');
    Route::post('/admin/door-locks', [WebAdminController::class, 'storeDoorLock'])->name('admin.doorlocks.store');
    Route::put('/admin/door-locks/{id}', [WebAdminController::class, 'updateDoorLock'])->name('admin.doorlocks.update');
    Route::post('/admin/access-cards/assign', [WebAdminController::class, 'assignAccessCard'])->name('admin.accesscards.assign');
    Route::delete('/admin/access-cards/{id}/revoke', [WebAdminController::class, 'revokeAccessCard'])->name('admin.accesscards.revoke');
});
