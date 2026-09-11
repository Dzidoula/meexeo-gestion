<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyDocumentController;
use App\Http\Controllers\PropertyPhotoController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantDocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/connexion', [LoginController::class, 'show'])->name('login');
Route::post('/connexion', [LoginController::class, 'store'])->name('login.store');
Route::post('/deconnexion', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/biens', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/locataires', [TenantController::class, 'index'])->name('tenants.index');

    Route::middleware('role:manager')->group(function () {
        Route::get('/biens/nouveau', [PropertyController::class, 'create'])->name('properties.create');
        Route::post('/biens', [PropertyController::class, 'store'])->name('properties.store');
        Route::get('/biens/{property}/modifier', [PropertyController::class, 'edit'])->name('properties.edit');
        Route::put('/biens/{property}', [PropertyController::class, 'update'])->name('properties.update');

        Route::post('/biens/{property}/photos', [PropertyPhotoController::class, 'store'])->name('properties.photos.store');
        Route::patch('/biens/{property}/photos/{photo}/principale', [PropertyPhotoController::class, 'primary'])->name('properties.photos.primary');
        Route::delete('/biens/{property}/photos/{photo}', [PropertyPhotoController::class, 'destroy'])->name('properties.photos.destroy');

        Route::post('/biens/{property}/documents', [PropertyDocumentController::class, 'store'])->name('properties.documents.store');
        Route::delete('/biens/{property}/documents/{document}', [PropertyDocumentController::class, 'destroy'])->name('properties.documents.destroy');

        Route::get('/locataires/nouveau', [TenantController::class, 'create'])->name('tenants.create');
        Route::post('/locataires', [TenantController::class, 'store'])->name('tenants.store');
        Route::get('/locataires/{tenant}/modifier', [TenantController::class, 'edit'])->name('tenants.edit');
        Route::put('/locataires/{tenant}', [TenantController::class, 'update'])->name('tenants.update');

        Route::post('/locataires/{tenant}/documents', [TenantDocumentController::class, 'store'])->name('tenants.documents.store');
        Route::delete('/locataires/{tenant}/documents/{document}', [TenantDocumentController::class, 'destroy'])->name('tenants.documents.destroy');
    });

    Route::get('/biens/{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::get('/locataires/{tenant}', [TenantController::class, 'show'])->name('tenants.show');
});
