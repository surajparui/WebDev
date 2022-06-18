<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::prefix('bookings')->group(function () {

    Route::get('', [BookingController::class, 'index']);
    Route::delete('', [BookingController::class, 'destroyAll']);
    Route::post('/aircrafts/{aircraft}', [BookingController::class, 'store']);
});
