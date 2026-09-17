<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ShowtimeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/coming-soon', [App\Http\Controllers\MovieController::class, 'comingSoon'])->name('coming-soon');
Route::get('/movies', [App\Http\Controllers\MovieController::class, 'index'])->name('movies.list');
Route::get('/movies/coming-soon', [MovieController::class, 'comingSoon'])->name('movies.coming-soon');
Route::get('/movies/{movie}', [MovieController::class, 'show'])->name('movies.show');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    // Dashboard route
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('movies.index');

    // User Booking routes
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::get('/bookings/movie/{movie}', [BookingController::class, 'bookMovie'])->name('bookings.book-movie');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/confirmation', [PaymentController::class, 'confirmation'])->name('bookings.confirmation');
    Route::get('/showtimes/{showtime}/seats', [BookingController::class, 'getAvailableSeats'])->name('showtimes.seats');

    // Payment routes
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{booking}/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/process', [PaymentController::class, 'process'])->name('payments.process');

    // Admin routes
    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['web', 'auth']], function () {
        Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
            // Admin Dashboard
            Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
            
            // Admin Settings
            Route::get('settings', [AdminController::class, 'settings'])->name('settings');
            Route::post('settings', [AdminController::class, 'updateSettings'])->name('settings.update');
            
            // Admin User Management
            Route::get('users', [AdminController::class, 'index'])->name('users.index');
            Route::get('users/{user}', [AdminController::class, 'show'])->name('users.show');
            Route::put('users/{user}', [AdminController::class, 'update'])->name('users.update');
            Route::delete('users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
            Route::patch('users/{user}/toggle-admin', [AdminController::class, 'toggleUserAdmin'])->name('users.toggle-admin');
            
            // Admin Booking Management
            Route::delete('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
            Route::resource('bookings', BookingController::class)->only(['index', 'show', 'destroy']);
            
            // Admin Movie Management
            Route::resource('movies', MovieController::class);
            
            // Admin Showtime Management
            Route::resource('showtimes', ShowtimeController::class);
        });
    });
});
