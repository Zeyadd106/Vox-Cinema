<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Showtime extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'movie_id',
        'hall_id',
        'date',
        'time',
        'price',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the formatted time.
     *
     * @return string
     */
    public function getFormattedTimeAttribute()
    {
        return Carbon::parse($this->time)->format('h:i A');
    }

    /**
     * Get the formatted date.
     *
     * @return string
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('D, M d, Y');
    }

    /**
     * Get the movie associated with the showtime.
     */
    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    /**
     * Get the hall associated with the showtime.
     */
    public function hall(): BelongsTo
    {
        return $this->belongsTo(Hall::class);
    }

    /**
     * Get the bookings for the showtime.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the available seats for this showtime.
     */
    public function availableSeats()
    {
        return $this->hall->seats()->whereDoesntHave('bookings', function ($query) {
            $query->where('showtime_id', $this->id);
        });
    }
}
