<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'row',
        'number',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['seat_number'];

    /**
     * Get the booking seats for the seat.
     */
    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class);
    }

    /**
     * Get the bookings for the seat.
     */
    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_seats');
    }

    /**
     * Get the seat number (e.g., "A1").
     */
    public function getSeatNumberAttribute()
    {
        return strtoupper($this->row) . $this->number;
    }
}
