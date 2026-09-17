<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Movie;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_bookings' => Booking::count(),
            'total_movies' => Movie::count(),
            'coming_soon_count' => Movie::where('status', 'coming_soon')->count(),
            'revenue' => Booking::where('payment_status', 'paid')->sum('total_price'),
            'recent_bookings' => Booking::with(['user', 'showtime.movie'])
                                    ->latest()
                                    ->take(5)
                                    ->get()
        ];

        $data = [
            'stats' => $stats,
            'movies' => Movie::where('status', '!=', 'coming_soon')->latest()->get(),
            'coming_soon' => Movie::where('status', 'coming_soon')->latest()->get(),
            'bookings' => Booking::with(['user', 'showtime.movie'])->latest()->take(10)->get(),
            'users' => User::latest()->get(),
        ];

        return view('admin.dashboard', $data);
    }
} 