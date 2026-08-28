<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Floor;
use Illuminate\Database\Seeder;

class FloorSeeder extends Seeder
{
    public function run(): void
    {
        $floorsByBuilding = [
            'MAIN' => [
                ['floor_number' => '1', 'name' => 'Ground Floor', 'description' => 'Emergency, admitting and diagnostics'],
                ['floor_number' => '2', 'name' => 'Second Floor', 'description' => 'Medical and surgical wards'],
                ['floor_number' => '3', 'name' => 'Third Floor', 'description' => 'Pediatric and OB-Gyne wards'],
            ],
            'ANNEX' => [
                ['floor_number' => '1', 'name' => 'Ground Floor', 'description' => 'Support services'],
                ['floor_number' => '2', 'name' => 'Second Floor', 'description' => 'Intensive care unit'],
            ],
            'OPD' => [
                ['floor_number' => '1', 'name' => 'Ground Floor', 'description' => 'Consultation clinics'],
            ],
        ];

        foreach ($floorsByBuilding as $buildingCode => $floors) {
            $building = Building::where('code', $buildingCode)->first();
            if (!$building) {
                continue;
            }

            foreach ($floors as $floor) {
                Floor::updateOrCreate(
                    ['building_id' => $building->id, 'floor_number' => $floor['floor_number']],
                    ['name' => $floor['name'], 'description' => $floor['description']]
                );
            }
        }
    }
}
