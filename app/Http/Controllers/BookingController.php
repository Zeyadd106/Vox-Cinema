<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Showtime;
use App\Models\Seat;
use App\Models\Movie;
use App\Notifications\BookingConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        if (auth()->user()->is_admin) {
            // Admin view - show all bookings with pagination
            $bookings = Booking::with(['user', 'showtime.movie', 'showtime.hall'])
                ->latest()
                ->paginate(10);
                
            return view('admin.bookings.index', compact('bookings'));
        }

        // Regular user view - show only their bookings
        $bookings = auth()->user()->bookings()
            ->with(['showtime.movie', 'showtime.hall'])
            ->latest()
            ->get();

        return view('bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        // Check if user is admin or the booking owner
        if (!auth()->user()->is_admin && auth()->id() !== $booking->user_id) {
            return redirect()->route('login')
                ->with('error', 'Please login to view this booking.');
        }

        // Load all necessary relationships with eager loading
        $booking->load([
            'showtime.movie',
            'showtime.hall',
            'seats',
            'user',
            'payment'
        ]);

        // Add computed properties
        $booking->total_price = number_format($booking->total_price, 2);
        $booking->status = $booking->status ?? 'pending';
        $booking->payment_status = $booking->payment_status ?? 'unpaid';

        // Determine which view to return based on user role
        if (auth()->user()->is_admin) {
            return view('admin.bookings.show', compact('booking'));
        }

        return view('bookings.show', compact('booking'));
    }

    public function bookMovie(Request $request, $movieId)
    {
        $movie = Movie::findOrFail($movieId);
        $showtimeId = $request->query('showtime');

        // Get showtimes for the next 7 days
        $showtimes = Showtime::where('movie_id', $movieId)
            ->where('date', '>=', now()->toDateString())
            ->where('date', '<=', now()->addDays(6)->toDateString())
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->groupBy('date');

        return view('bookings.book', compact('movie', 'showtimeId', 'showtimes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'showtime_id' => 'required|exists:showtimes,id',
            'seat_ids' => 'required|string', // JSON string of seat IDs
            'total_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Decode seat IDs
        $seatIds = json_decode($request->seat_ids);

        if (empty($seatIds)) {
            return redirect()->back()
                ->with('error', 'Please select at least one seat')
                ->withInput();
        }

        // Check if seats are available
        $showtime = Showtime::findOrFail($request->showtime_id);
        $bookedSeats = BookingSeat::whereHas('booking', function ($query) use ($showtime) {
            $query->where('showtime_id', $showtime->id);
        })->pluck('seat_id')->toArray();

        $unavailableSeats = array_intersect($bookedSeats, $seatIds);

        if (count($unavailableSeats) > 0) {
            return redirect()->back()
                ->with('error', 'Some seats are no longer available. Please select different seats.')
                ->withInput();
        }

        // Create booking
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'showtime_id' => $request->showtime_id,
            'total_price' => $request->total_price,
            'payment_status' => 'pending',
            'booking_reference' => 'VOX' . Str::upper(Str::random(8)),
        ]);

        // Add seats to booking
        foreach ($seatIds as $seatId) {
            BookingSeat::create([
                'booking_id' => $booking->id,
                'seat_id' => $seatId,
            ]);
        }

        // Send booking confirmation email
        Auth::user()->notify(new BookingConfirmation($booking));

        // Redirect to payment page with booking ID
        return redirect()->route('payments.create', ['booking' => $booking->id]);
    }

    public function confirmation($id)
    {
        $booking = Booking::with(['showtime.movie', 'seats', 'payment'])
            ->findOrFail($id);

        // Check if the booking belongs to the authenticated user
        if ($booking->user_id !== Auth::id() && !Auth::user()->is_admin) {
            return redirect()->route('bookings.index')
                ->with('error', 'Unauthorized access to booking');
        }

        return view('bookings.confirmation', compact('booking'));
    }

    public function getAvailableSeats($showtimeId)
    {
        $showtime = Showtime::findOrFail($showtimeId);

        // Get all seats
        $allSeats = Seat::all();

        // Get booked seats for this showtime
        $bookedSeats = BookingSeat::whereHas('booking', function ($query) use ($showtimeId) {
            $query->where('showtime_id', $showtimeId);
        })->pluck('seat_id')->toArray();

        // Mark seats as available or booked
        $seats = $allSeats->map(function ($seat) use ($bookedSeats) {
            $seat->is_available = !in_array($seat->id, $bookedSeats);
            return $seat;
        });

        return response()->json(['seats' => $seats]);
    }

    public function create()
    {
        $movies = Movie::where('status', 'current')->latest()->get();

        return view('bookings.create', compact('movies'));
    }

    public function cancel(Booking $booking)
    {
        if (!auth()->user()->is_admin && auth()->id() !== $booking->user_id) {
            return back()->with('error', 'Unauthorized action.');
        }

        if ($booking->status !== 'Pending') {
            return back()->with('error', 'Only pending bookings can be cancelled.');
        }

        try {
            DB::transaction(function () use ($booking) {
                // Release the seats
                $booking->seats()->update(['is_booked' => false]);
                
                // Update booking status
                $booking->update(['status' => 'Cancelled']);
                
                // If payment exists and is pending, cancel it
                if ($booking->payment && $booking->payment->status === 'pending') {
                    $booking->payment->update(['status' => 'cancelled']);
                }
            });

            return back()->with('success', 'Booking cancelled successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel booking. Please try again.');
        }
    }

    public function destroy(Booking $booking)
    {
        try {
            DB::transaction(function () use ($booking) {
                $booking->seats()->detach();
                if ($booking->payment) {
                    $booking->payment->delete();
                }
                $booking->delete();
            });

            if (request()->routeIs('admin.*')) {
                return redirect()->route('admin.bookings.index')
                    ->with('success', 'Booking deleted successfully.');
            }

            return redirect()->route('bookings.index')
                ->with('success', 'Booking deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete booking. Please try again.');
        }
    }
}
