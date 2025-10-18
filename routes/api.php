<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AirportController;
use App\Http\Controllers\FlightSearchController;
use App\Http\Controllers\DevSeedController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::prefix('auth')->group(function () {
  Route::post('/register', [AuthenticatedSessionController::class, 'register']);
  Route::post('/login',    [AuthenticatedSessionController::class, 'login']);
  Route::post('/logout',   [AuthenticatedSessionController::class, 'logout'])->middleware('auth:sanctum');
});

Route::get('/airports', [AirportController::class, 'index']);
Route::get('/flights/search', [FlightSearchController::class, 'search']);
Route::get('/flights/{flight}', [FlightSearchController::class, 'show']);


if (app()->environment('local')) {
    Route::match(['POST','GET'], '/dev/seed-example', [DevSeedController::class, 'example']);

}