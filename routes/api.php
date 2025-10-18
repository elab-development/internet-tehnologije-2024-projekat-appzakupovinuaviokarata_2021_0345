<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AirportController;
use App\Http\Controllers\FlightSearchController;
use App\Http\Controllers\DevSeedController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BookingController;

Route::prefix('auth')->group(function () {
  Route::post('/register', [AuthenticatedSessionController::class, 'register']);
  Route::post('/login',    [AuthenticatedSessionController::class, 'login']);
  Route::post('/logout',   [AuthenticatedSessionController::class, 'logout'])->middleware('auth:sanctum');
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