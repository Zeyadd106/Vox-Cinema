<?php

namespace Database\Seeders;

use App\Models\Seat;
use Illuminate\Database\Seeder;

class SeatsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        $seatsPerRow = 10;

        foreach ($rows as $row) {
            for ($i = 1; $i <= $seatsPerRow; $i++) {
                Seat::create([
                    'row' => $row,
                    'number' => $i,
                ]);
            }
        }
    }
}
