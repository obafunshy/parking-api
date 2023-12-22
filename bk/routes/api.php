<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/availability', [BookingController::class, 'checkAvailability']);
Route::get('/bookings', [BookingController::class, 'index']);
Route::get('/booking',  [BookingController::class, 'show']);
Route::post('/booking',  [BookingController::class, 'store']);
Route::put('/booking/{bookingId}',  [BookingController::class, 'update']);
Route::delete('/booking/{bookingId}',  [BookingController::class, 'destroy']);
