<?php

use App\Http\Controllers\Api\HotelRoomTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/room-types', [HotelRoomTypeController::class, 'index']);
    Route::get('/room-types/{hotelRoomType}', [HotelRoomTypeController::class, 'show']);
});
