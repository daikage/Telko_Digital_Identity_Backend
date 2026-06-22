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
