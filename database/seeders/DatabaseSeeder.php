<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            MoviesTableSeeder::class,
            SeatsTableSeeder::class,
            ShowtimesTableSeeder::class,
            BookingsTableSeeder::class,
            HallSeeder::class,
        ]);
    }
}
