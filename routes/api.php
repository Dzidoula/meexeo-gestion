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
    Route::post('/touvalem-sync/faqs', [TouvalemSyncController::class, 'faqs']);
    Route::post('/touvalem-sync/faqs/delete', [TouvalemSyncController::class, 'faqsDestroy']);
    Route::post('/touvalem-sync/contact-messages', [TouvalemSyncController::class, 'contactMessages']);
    Route::post('/touvalem-sync/contact-messages/delete', [TouvalemSyncController::class, 'contactMessagesDestroy']);
    Route::post('/touvalem-sync/newsletter-subscribers', [TouvalemSyncController::class, 'newsletterSubscribers']);
    Route::post('/touvalem-sync/newsletter-subscribers/delete', [TouvalemSyncController::class, 'newsletterSubscribersDestroy']);
    Route::post('/touvalem-sync/promo-codes', [TouvalemSyncController::class, 'promoCodes']);
    Route::post('/touvalem-sync/promo-codes/delete', [TouvalemSyncController::class, 'promoCodesDestroy']);
});
