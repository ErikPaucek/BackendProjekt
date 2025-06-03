<?php

namespace Database\Seeders;

use App\Models\Subpage;
use App\Models\ConferenceYear as Year;
use Illuminate\Database\Seeder;

class SubpagesSeeder extends Seeder
{
    public function run(): void
    {
        $year2022 = Year::where('year', 2022)->first();
        $year2023 = Year::where('year', 2023)->first();
        $year2024 = Year::where('year', 2024)->first();

        $subpages = [
            [
                'year_id' => $year2022?->id,
                'title' => 'O konferencii',
                'slug' => 'o-konferencii-2022',
                'content' => 'Informácie o konferencii.'
            ],
            [
                'year_id' => $year2022?->id,
                'title' => 'Program',
                'slug' => 'program-2022',
                'content' => 'Program konferencie.'
            ],
            [
                'year_id' => $year2022?->id,
                'title' => 'Kontakt',
                'slug' => 'kontakt-2022',
                'content' => 'Kontaktné informácie.'
            ],
            [
                'year_id' => $year2023?->id,
                'title' => 'O konferencii',
                'slug' => 'o-konferencii-2023',
                'content' => 'Informácie o konferencii 2023.'
            ],
            [
                'year_id' => $year2023?->id,
                'title' => 'Program',
                'slug' => 'program-2023',
                'content' => 'Program konferencie 2023.'
            ],
            [
                'year_id' => $year2023?->id,
                'title' => 'Kontakt',
                'slug' => 'kontakt-2023',
                'content' => 'Kontaktné informácie 2023.'
            ],
            [
                'year_id' => $year2024?->id,
                'title' => 'O konferencii',
                'slug' => 'o-konferencii-2024',
                'content' => 'Informácie o konferencii 2024.'
            ],
            [
                'year_id' => $year2024?->id,
                'title' => 'Program',
                'slug' => 'program-2024',
                'content' => 'Program konferencie 2024.'
            ],
            [
                'year_id' => $year2024?->id,
                'title' => 'Kontakt',
                'slug' => 'kontakt-2024',
                'content' => 'Kontaktné informácie 2024.'
            ],
        ];

        foreach ($subpages as $subpage) {
            if ($subpage['year_id']) {
                Subpage::create($subpage);
            }
        }
    }
}