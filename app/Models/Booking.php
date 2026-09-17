<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'showtime_id',
        'total_price',
        'status',
        'payment_status',
        'payment_method',
        'transaction_id',
        'booking_reference',
        'paid_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the showtime for the booking.
     */
    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    /**
     * Get the movie through the showtime.
     */
    public function movie()
    {
        return $this->hasOneThrough(
            Movie::class,
            Showtime::class,
            'id', // Foreign key on showtimes table
            'id', // Foreign key on movies table
            'showtime_id', // Local key on bookings table
            'movie_id' // Local key on showtimes table
        );
    }

    /**
     * Get the booking seats for the booking.
     */
    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class);
    }

    /**
     * Get the seats for the booking.
     */
    public function seats()
    {
        return $this->belongsToMany(Seat::class, 'booking_seats')
            ->withTimestamps();
    }

    /**
     * Get the payment associated with the booking.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
