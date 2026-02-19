<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing events
        Event::truncate();

        $events = [
            // 2023
            [
                'name' => 'Posko Lebaran 2023',
                'start_date' => '2023-04-14', // H-8
                'end_date' => '2023-05-02',   // H+7 (Idul Fitri ~22 Apr)
                'color' => '#10b981', // Emerald
            ],
            [
                'name' => 'Posko Nataru 2023/24',
                'start_date' => '2023-12-19', // ~H-6 Natal
                'end_date' => '2024-01-04',   // ~H+3 Tahun Baru
                'color' => '#f43f5e', // Rose
            ],

            // 2024
            [
                'name' => 'Posko Lebaran 2024',
                'start_date' => '2024-04-03', // H-7
                'end_date' => '2024-04-18',   // H+7 (Idul Fitri ~10 Apr)
                'color' => '#10b981',
            ],
            [
                'name' => 'Posko Nataru 2024/25',
                'start_date' => '2024-12-19',
                'end_date' => '2025-01-04',
                'color' => '#f43f5e',
            ],

            // 2025 (Projected based on Hijri)
            [
                'name' => 'Posko Lebaran 2025',
                'start_date' => '2025-03-24', // Idul Fitri ~31 Mar
                'end_date' => '2025-04-08',
                'color' => '#10b981',
            ],
            [
                'name' => 'Posko Nataru 2025/26',
                'start_date' => '2025-12-19',
                'end_date' => '2026-01-04',
                'color' => '#f43f5e',
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
