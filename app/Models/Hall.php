<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'description',
        'status'
    ];

    /**
     * Get all showtimes for this hall
     */
    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    /**
     * Get all seats in this hall
     */
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
} 