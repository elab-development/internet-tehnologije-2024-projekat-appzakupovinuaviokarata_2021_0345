<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AirportController;
use App\Http\Controllers\FlightSearchController;
use App\Http\Controllers\DevSeedController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::prefix('auth')->group(function () {
    Route::post('register', [RegisteredUserController::class, 'apiStore']);
    Route::post('login',    [AuthenticatedSessionController::class, 'apiStore']);
    Route::post('logout',   [AuthenticatedSessionController::class, 'apiDestroy'])
        ->middleware('auth:sanctum');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'apiStore'])
        ->middleware('guest');
    Route::post('reset-password',  [NewPasswordController::class, 'apiStore'])
        ->middleware('guest');
});



Route::middleware('auth:sanctum')->group(function () {
    Route::get   ('/bookings',           [BookingController::class, 'index']);
    Route::post  ('/bookings',           [BookingController::class, 'store']);
    Route::get   ('/bookings/{booking}', [BookingController::class, 'show']);
    Route::put   ('/bookings/{booking}', [BookingController::class, 'update']);
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);
});

Route::get('/airports', [AirportController::class, 'index']);
Route::get('/flights/search', [FlightSearchController::class, 'search']);
Route::get('/flights/{flight}', [FlightSearchController::class, 'show']);


if (app()->environment('local')) {
    Route::match(['POST','GET'], '/dev/seed-example', [DevSeedController::class, 'example']);

}