<?php

namespace Database\Seeders;

use App\Models\PertinentSignsAndSymptomsList;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PertinentSignsAndSymptomsListSeeder extends Seeder
{
    /**
     * Default pertinent signs & symptoms. The named items get numeric codes
     * (their position); "Others" gets the code "X". Selected items are stored
     * on a patient form as their codes joined by ";", e.g. "1;4;5;X".
     */
    public function run(): void
    {
        $names = [
            'Altered mental sensorium',
            'Abdominal cramp/pain',
            'Anorexia',
            'Bleeding gums',
            'Body weakness',
            'Blurring of vision',
            'Chest pain/discomfort',
            'Constipation',
            'Cough',
            'Diarrhea',
            'Dizziness',
            'Dysphagia',
            'Dyspnea',
            'Dysuria',
            'Epistaxis',
            'Fever',
            'Frequency of urination',
            'Headache',
            'Hematemesis',
            'Hematuria',
            'Hemoptysis',
            'Irritability',
            'Jaundice',
            'Lower extremity edema',
            'Myalgia',
            'Orthopnea',
            'Pain',
            'Palpitations',
            'Seizures',
            'Skin rashes',
            'Stool, bloody/black tarry/mucoid',
            'Sweating',
            'Urgency',
            'Vomiting',
            'Weight loss',
        ];

        foreach ($names as $index => $name) {
            PertinentSignsAndSymptomsList::updateOrCreate(
                ['code' => (string) ($index + 1)],
                ['name' => $name, 'status' => true, 'pid' => (string) Str::uuid()]
            );
        }

        PertinentSignsAndSymptomsList::updateOrCreate(
            ['code' => 'X'],
            ['name' => 'Others', 'status' => true, 'pid' => (string) Str::uuid()]
        );
    }
}
