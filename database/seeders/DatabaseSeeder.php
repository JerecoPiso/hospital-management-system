<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'role_id' => 1,
            'pid' => Str::uuid()->toString(),
            'firstname' => 'John',
            'lastname' => "Doe",
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'date_of_birth' => fake()->date(),

        ]);
        // User::factory(9)->create();
        $this->call([
            PatientTypeSeeder::class,
            BuildingSeeder::class,
            FloorSeeder::class,
            WardSeeder::class,
            RoomSeeder::class,
            BedSeeder::class,
            StationSeeder::class,
            PertinentSignsAndSymptomsListSeeder::class,
        ]);
    }
}
