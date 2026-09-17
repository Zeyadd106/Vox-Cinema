<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;

class MoviesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = [
            [
                'title' => 'Minecraft',
                'description' => 'A group of unlikely heroes embark on a dangerous quest to save their blocky Overworld.',
                'duration' => '2h 5m',
                'poster_path' => 'posters/minecraft.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=MmB9b5njVbA',
                'genre' => 'Animation, Adventure',
                'rating' => 'PG',
                'status' => 'current',
                'release_date' => '2025-04-01',
            ],
            [
                'title' => 'Sinners',
                'description' => 'A psychological thriller about a group of people confronting their past sins.',
                'duration' => '2h 15m',
                'poster_path' => 'posters/sinners.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example',
                'genre' => 'Thriller, Drama',
                'rating' => 'R',
                'status' => 'current',
                'release_date' => '2025-03-15',
            ],
            [
                'title' => 'Cloning',
                'description' => 'A sci-fi romance about cloning and the ethical dilemmas it presents.',
                'duration' => '1h 55m',
                'poster_path' => 'posters/cloning.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example2',
                'genre' => 'Sci-Fi, Romance',
                'rating' => 'PG-13',
                'status' => 'current',
                'release_date' => '2025-02-28',
            ],
            [
                'title' => 'Thunderbolts',
                'description' => 'A team of antiheroes is assembled for a dangerous mission.',
                'duration' => '2h 30m',
                'poster_path' => 'posters/thunderbolts.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example3',
                'genre' => 'Action, Superhero',
                'rating' => 'PG-13',
                'status' => 'coming_soon',
                'release_date' => '2025-07-26',
            ],
            [
                'title' => 'Final Destination: Bloodlines',
                'description' => 'Death returns to claim those who escaped their fate.',
                'duration' => '1h 50m',
                'poster_path' => 'posters/final_destination.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example4',
                'genre' => 'Horror, Thriller',
                'rating' => 'R',
                'status' => 'coming_soon',
                'release_date' => '2025-08-15',
            ],
            [
                'title' => 'Mission: Impossible - The Final Reckoning',
                'description' => 'Ethan Hunt faces his most dangerous mission yet.',
                'duration' => '2h 20m',
                'poster_path' => 'posters/mission_impossible.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=example5',
                'genre' => 'Action, Spy',
                'rating' => 'PG-13',
                'status' => 'coming_soon',
                'release_date' => '2025-09-01',
            ],
        ];

        foreach ($movies as $movie) {
            Movie::create($movie);
        }
    }
}
