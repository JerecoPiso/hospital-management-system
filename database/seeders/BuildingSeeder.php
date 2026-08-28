<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    public function run(): void
    {
        $buildings = [
            ['code' => 'MAIN', 'name' => 'Main Building', 'description' => 'Primary hospital building'],
            ['code' => 'ANNEX', 'name' => 'Annex Building', 'description' => 'Secondary building for extended services'],
            ['code' => 'OPD', 'name' => 'Outpatient Building', 'description' => 'Outpatient department and clinics'],
        ];

        foreach ($buildings as $building) {
            Building::updateOrCreate(
                ['code' => $building['code']],
                ['name' => $building['name'], 'description' => $building['description']]
            );
        }
    }
}
