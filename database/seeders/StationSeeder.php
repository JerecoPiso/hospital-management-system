<?php

namespace Database\Seeders;

use App\Models\Station;
use App\Models\Ward;
use Illuminate\Database\Seeder;

class StationSeeder extends Seeder
{
    public function run(): void
    {
        $stations = [
            ['ward' => 'ER', 'name' => 'ER Nursing Station', 'description' => 'Emergency ward nursing station'],
            ['ward' => 'MED', 'name' => 'Medical Nursing Station', 'description' => 'Medical ward nursing station'],
            ['ward' => 'SUR', 'name' => 'Surgical Nursing Station', 'description' => 'Surgical ward nursing station'],
            ['ward' => 'PED', 'name' => 'Pediatric Nursing Station', 'description' => 'Pediatric ward nursing station'],
            ['ward' => 'OB', 'name' => 'OB-Gyne Nursing Station', 'description' => 'OB-Gyne ward nursing station'],
            ['ward' => 'ICU', 'name' => 'ICU Nursing Station', 'description' => 'Intensive care unit nursing station'],
            ['ward' => 'OPD', 'name' => 'OPD Nursing Station', 'description' => 'Outpatient department nursing station'],
        ];

        foreach ($stations as $station) {
            $ward = Ward::where('code', $station['ward'])->first();
            if (!$ward) {
                continue;
            }

            Station::updateOrCreate(
                ['ward_id' => $ward->id, 'name' => $station['name']],
                ['description' => $station['description']]
            );
        }
    }
}
