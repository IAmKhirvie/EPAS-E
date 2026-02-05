<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InformationSheet;
use App\Models\Topic;

class TopicsSeeder extends Seeder
{
    public function run(): void
    {
        $informationSheets = InformationSheet::all();

        foreach ($informationSheets as $sheet) {
            if ($sheet->sheet_number == '1.1') {
                Topic::create([
                    'information_sheet_id' => $sheet->id,
                    'title' => 'Introduction to Electronics and Electricity',
                    'content' => 'Content for introduction topic...',
                    'order' => 1,
                    'estimated_time' => 30
                ]);

                Topic::create([
                    'information_sheet_id' => $sheet->id,
                    'title' => 'Electric History',
                    'content' => 'Content for electric history...',
                    'order' => 2,
                    'estimated_time' => 25
                ]);

                // Add more topics as needed
            }
        }
    }
}