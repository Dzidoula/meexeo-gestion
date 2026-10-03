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
        Route::get('/mon-contrat',         [\App\Http\Controllers\TenantPortal\LeaseController::class, 'show'])->name('lease');
        Route::get('/paiements',           [\App\Http\Controllers\TenantPortal\PaymentController::class, 'index'])->name('payments');
        Route::get('/paiements/nouveau',   [\App\Http\Controllers\TenantPortal\PaymentController::class, 'create'])->name('payments.create');
        Route::post('/paiements',          [\App\Http\Controllers\TenantPortal\PaymentController::class, 'store'])->name('payments.store');
        Route::get('/preuves/envoyer',     [\App\Http\Controllers\TenantPortal\ProofController::class, 'create'])->name('proofs.create');
        Route::post('/preuves',            [\App\Http\Controllers\TenantPortal\ProofController::class, 'store'])->name('proofs.store');
        Route::get('/reparations',         [\App\Http\Controllers\TenantPortal\RepairController::class, 'index'])->name('repairs');
        Route::get('/reparations/nouveau', [\App\Http\Controllers\TenantPortal\RepairController::class, 'create'])->name('repairs.create');
        Route::post('/reparations',        [\App\Http\Controllers\TenantPortal\RepairController::class, 'store'])->name('repairs.store');
        Route::get('/reparations/{repair}',[\App\Http\Controllers\TenantPortal\RepairController::class, 'show'])->name('repairs.show');
        Route::get('/loyers',              [\App\Http\Controllers\TenantPortal\RentController::class, 'index'])->name('rents');
        Route::get('/profil',              [\App\Http\Controllers\TenantPortal\ProfileController::class, 'show'])->name('profile');
        Route::patch('/profil',            [\App\Http\Controllers\TenantPortal\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/documents',           [\App\Http\Controllers\TenantPortal\DocumentController::class, 'index'])->name('documents');
        Route::get('/documents/{document}',[\App\Http\Controllers\TenantPortal\DocumentController::class, 'download'])->name('documents.download');
        Route::get('/messages',            [\App\Http\Controllers\TenantPortal\MessageController::class, 'index'])->name('messages');
        Route::get('/messages/{message}',  [\App\Http\Controllers\TenantPortal\MessageController::class, 'show'])->name('messages.show');
        Route::post('/messages',           [\App\Http\Controllers\TenantPortal\MessageController::class, 'store'])->name('messages.store');
        Route::get('/notifications',       [\App\Http\Controllers\TenantPortal\NotificationController::class, 'index'])->name('notifications');
        Route::get('/contact',             [\App\Http\Controllers\TenantPortal\ContactController::class, 'index'])->name('contact');
        Route::post('/contact',            [\App\Http\Controllers\TenantPortal\ContactController::class, 'store'])->name('contact.store');
        Route::get('/avis-echeance',       [\App\Http\Controllers\TenantPortal\RentController::class, 'notice'])->name('rent-notice');
    });
});
