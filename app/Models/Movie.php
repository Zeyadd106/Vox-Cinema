<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'duration',
        'release_date',
        'poster_path',
        'trailer_url',
        'genre',
        'director',
        'cast',
        'rating',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'release_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'cast' => 'array'
    ];

    /**
     * Get the showtimes for the movie.
     */
    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    /**
     * Get all bookings for this movie through showtimes.
     */
    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Showtime::class);
    }

    /**
     * Get upcoming showtimes for this movie.
     */
    public function upcomingShowtimes()
    {
        return $this->showtimes()
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('time');
    }
}
