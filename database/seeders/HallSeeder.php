<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hall;

class HallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $halls = [
            [
                'name' => 'Hall A',
                'capacity' => 100,
                'description' => 'Standard Cinema Hall',
                'status' => 'active',
            ],
            [
                'name' => 'Hall B',
                'capacity' => 150,
                'description' => 'Premium Cinema Hall with Dolby Atmos',
                'status' => 'active',
            ],
            [
                'name' => 'Hall C',
                'capacity' => 80,
                'description' => 'Intimate Cinema Experience',
                'status' => 'active',
            ],
        ];

        foreach ($halls as $hall) {
            Hall::create($hall);
        }
    }
} 