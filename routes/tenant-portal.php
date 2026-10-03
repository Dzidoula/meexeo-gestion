<?php

use App\Http\Controllers\TenantPortal\Auth\LoginController;
use App\Http\Controllers\TenantPortal\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('espace-locataire')->name('tenant-portal.')->group(function () {
    // Auth (unauthenticated)
    Route::get('/connexion',     [LoginController::class, 'showForm'])->name('login');
    Route::post('/connexion',    [LoginController::class, 'sendOtp'])->name('send-otp');
    Route::get('/verification',  [LoginController::class, 'showVerify'])->name('verify');
    Route::post('/verification', [LoginController::class, 'verifyOtp'])->name('verify.submit');
    Route::post('/deconnexion',  [LoginController::class, 'logout'])->name('logout');
    Route::get('/pas-de-bail',   fn () => view('tenant-portal.no-lease'))->name('no-lease');

    // Protected routes
    Route::middleware(['auth:tenant', 'tenant.has-lease'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    });
});
