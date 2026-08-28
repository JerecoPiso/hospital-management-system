<?php

namespace Database\Seeders;

use App\Models\PatientType;
use Illuminate\Database\Seeder;

class PatientTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'IM', 'name' => 'Internal Medicine', 'description' => 'Adult non-surgical medical care'],
            ['code' => 'PEDIA', 'name' => 'Pediatrics', 'description' => 'Medical care for infants, children and adolescents'],
            ['code' => 'OB', 'name' => 'Obstetrics', 'description' => 'Pregnancy, childbirth and postpartum care'],
            ['code' => 'GYNE', 'name' => 'Gynecology', 'description' => 'Female reproductive system care'],
            ['code' => 'SURG', 'name' => 'Surgery', 'description' => 'General surgical care'],
            ['code' => 'ORTHO', 'name' => 'Orthopedics', 'description' => 'Musculoskeletal and bone care'],
            ['code' => 'ENT', 'name' => 'Otorhinolaryngology (ENT)', 'description' => 'Ear, nose and throat care'],
            ['code' => 'OPHTHA', 'name' => 'Ophthalmology', 'description' => 'Eye care'],
            ['code' => 'FM', 'name' => 'Family Medicine', 'description' => 'Primary care for all ages'],
            ['code' => 'PSYCH', 'name' => 'Psychiatry', 'description' => 'Mental health care'],
            ['code' => 'ER', 'name' => 'Emergency Medicine', 'description' => 'Acute and emergency care'],
        ];

        foreach ($types as $type) {
            PatientType::updateOrCreate(
                ['code' => $type['code']],
                ['name' => $type['name'], 'description' => $type['description']]
            );
        }

        // Remove the previous admission-class placeholders that were seeded before.
        PatientType::whereIn('code', ['IN', 'OUT', 'OBS', 'DAY'])->forceDelete();
    }
}
