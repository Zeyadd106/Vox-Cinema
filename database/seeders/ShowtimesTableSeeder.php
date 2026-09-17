<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Showtime;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ShowtimesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentMovies = Movie::where('status', 'current')->get();

        $times = ['10:00', '12:30', '15:00', '17:30', '20:00', '22:30'];

        foreach ($currentMovies as $movie) {
            // Create showtimes for the next 7 days
            for ($i = 0; $i < 7; $i++) {
                $date = Carbon::now()->addDays($i)->format('Y-m-d');

                // Add 3-4 showtimes per day
                $dailyTimes = array_slice($times, 0, rand(3, 4));

                foreach ($dailyTimes as $time) {
                    Showtime::create([
                        'movie_id' => $movie->id,
                        'date' => $date,
                        'time' => $time,
                    ]);
                }
            }
        }
    }
}
