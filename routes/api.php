<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/movies', [MovieController::class, 'index']);
Route::get('/movies/coming-soon', [MovieController::class, 'comingSoon']);
Route::get('/movies/{movie}', [MovieController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Booking routes
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
    Route::delete('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
    Route::get('/showtimes/{showtime}/seats', [BookingController::class, 'getAvailableSeats']);
    Route::get('/showtimes/movie/{movie}', [ShowtimeController::class, 'getShowtimesByMovie']);

    // Payment routes
    Route::post('/payments/process', [PaymentController::class, 'processPayment']);

    // Admin routes
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::get('/bookings', [AdminController::class, 'bookings']);
        Route::patch('/users/{user}/toggle-admin', [AdminController::class, 'toggleUserAdmin']);
        Route::resource('/movies', MovieController::class)->except(['index', 'show']);
        Route::resource('/showtimes', ShowtimeController::class);
    });

    Route::post('/notify-me', [MovieController::class, 'setNotification']);
});
