<?php

namespace Database\Seeders;

use App\Models\ConferenceYear as Year;
use Illuminate\Database\Seeder;

class YearsSeeder extends Seeder
{
    public function run(): void
    {
        $years = [
            ['year' => 2022],
            ['year' => 2023],
            ['year' => 2024],
        ];

        foreach ($years as $year) {
            Year::create($year);
        }
    }
}