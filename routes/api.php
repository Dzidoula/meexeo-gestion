<?php

use App\Http\Controllers\Api\HotelBookingController;
use App\Http\Controllers\Api\HotelRoomTypeController;
use App\Http\Controllers\Api\TouvalemSyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/room-types', [HotelRoomTypeController::class, 'index']);
    Route::get('/room-types/{hotelRoomType}', [HotelRoomTypeController::class, 'show']);
    Route::post('/bookings', [HotelBookingController::class, 'store']);
    Route::post('/touvalem-sync/bookings', [TouvalemSyncController::class, 'bookings']);
    Route::post('/touvalem-sync/galleries', [TouvalemSyncController::class, 'galleries']);
    Route::post('/touvalem-sync/galleries/delete', [TouvalemSyncController::class, 'galleriesDestroy']);
    Route::post('/touvalem-sync/testimonials', [TouvalemSyncController::class, 'testimonials']);
    Route::post('/touvalem-sync/testimonials/delete', [TouvalemSyncController::class, 'testimonialsDestroy']);
});
