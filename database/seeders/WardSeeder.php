<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Ward;
use Illuminate\Database\Seeder;

class WardSeeder extends Seeder
{
    public function run(): void
    {
        $wards = [
            ['code' => 'ER', 'name' => 'Emergency Ward', 'building' => 'MAIN', 'floor_number' => '1'],
            ['code' => 'MED', 'name' => 'Medical Ward', 'building' => 'MAIN', 'floor_number' => '2'],
            ['code' => 'SUR', 'name' => 'Surgical Ward', 'building' => 'MAIN', 'floor_number' => '2'],
            ['code' => 'PED', 'name' => 'Pediatric Ward', 'building' => 'MAIN', 'floor_number' => '3'],
            ['code' => 'OB', 'name' => 'OB-Gyne Ward', 'building' => 'MAIN', 'floor_number' => '3'],
            ['code' => 'ICU', 'name' => 'Intensive Care Unit', 'building' => 'ANNEX', 'floor_number' => '2'],
            ['code' => 'OPD', 'name' => 'Outpatient Ward', 'building' => 'OPD', 'floor_number' => '1'],
        ];

        foreach ($wards as $ward) {
            $building = Building::where('code', $ward['building'])->first();
            if (!$building) {
                continue;
            }

            $floor = Floor::where('building_id', $building->id)
                ->where('floor_number', $ward['floor_number'])
                ->first();
            if (!$floor) {
                continue;
            }

            Ward::updateOrCreate(
                ['code' => $ward['code']],
                ['floor_id' => $floor->id, 'name' => $ward['name']]
            );
        }
    }
}
