<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\User;
use App\Models\Showtime;
use App\Models\Seat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookingsTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $showtimes = Showtime::all();
        $seats = Seat::all();

        // Create 5 sample bookings
        for ($i = 0; $i < 5; $i++) {
            $user = $users->random();
            $showtime = $showtimes->random();
            $numSeats = rand(1, 4);
            $selectedSeats = $seats->random($numSeats);
            
            $booking = Booking::create([
                'user_id' => $user->id,
                'showtime_id' => $showtime->id,
                'total_price' => $showtime->price * $numSeats,
                'status' => collect(['pending', 'confirmed', 'cancelled'])->random(),
                'payment_status' => collect(['paid', 'pending', 'failed'])->random(),
                'payment_method' => collect(['credit_card', 'debit_card', 'paypal'])->random(),
                'transaction_id' => Str::random(10),
                'booking_reference' => 'VOX' . Str::upper(Str::random(8)),
            ]);

            // Attach seats to booking
            $booking->seats()->attach($selectedSeats->pluck('id'));
        }
    }
} 