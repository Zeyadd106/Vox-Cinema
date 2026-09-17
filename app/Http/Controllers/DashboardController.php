<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the user dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get upcoming bookings (future dates)
        $upcomingBookings = Booking::where('user_id', $user->id)
            ->whereHas('showtime', function($query) {
                $query->where('date', '>=', Carbon::today());
            })
            ->with(['movie', 'showtime', 'seats'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get booking history (past dates)
        $bookingHistory = Booking::where('user_id', $user->id)
            ->whereHas('showtime', function($query) {
                $query->where('date', '<', Carbon::today());
            })
            ->with(['movie', 'showtime', 'seats'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('dashboard.index', compact('upcomingBookings', 'bookingHistory'));
    }
} 