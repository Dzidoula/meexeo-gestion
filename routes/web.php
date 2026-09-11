<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/connexion', [LoginController::class, 'show'])->name('login');
Route::post('/connexion', [LoginController::class, 'store'])->name('login.store');
Route::post('/deconnexion', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/biens', [PropertyController::class, 'index'])->name('properties.index');

    Route::middleware('role:manager')->group(function () {
        Route::get('/biens/nouveau', [PropertyController::class, 'create'])->name('properties.create');
        Route::post('/biens', [PropertyController::class, 'store'])->name('properties.store');
        Route::get('/biens/{property}/modifier', [PropertyController::class, 'edit'])->name('properties.edit');
        Route::put('/biens/{property}', [PropertyController::class, 'update'])->name('properties.update');
    });
});
