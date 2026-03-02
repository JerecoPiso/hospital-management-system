<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();   // MAIN, ANNEX
            $table->string('code')->unique();   // MAIN, ANNEX
            $table->string('name');
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->foreignId('building_id')
                ->constrained('buildings')
                ->cascadeOnDelete();
            $table->string('floor_number');     // 1, 2, 3, B1
            $table->string('name')->nullable(); // 1st Floor, Basement
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->foreignId('floor_id')
                ->constrained('floors')
                ->cascadeOnDelete();
            $table->string('code')->unique(); // MED, SURG, PEDS
            $table->string('name'); // Medical Ward
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->foreignId('ward_id')
                ->constrained('wards')
                ->cascadeOnDelete();
            $table->string('room_number'); // 201, 305A
            $table->string('room_type')->nullable(); // private, semi-private, isolation
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')
                ->constrained('rooms')
                ->cascadeOnDelete();
            $table->string('bed_number'); // A, B, 1, 2
            $table->enum('status', [
                'available',
                'occupied',
                'cleaning',
                'maintenance'
            ])->default('available');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->foreignId('ward_id')
                ->constrained('wards')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('description')->nullable(); // private, semi-private, isolation
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('patient_types', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->string('code', 10);
            $table->string('name');
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('patient_cases', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->foreignId('patient_id')
                ->constrained('patients')
                ->cascadeOnDelete();
            $table->foreignId('station_id')
                ->nullable()
                ->constrained('stations')
                ->cascadeOnDelete();
            $table->foreignId('bed_id')
                ->nullable()
                ->constrained('beds')
                ->cascadeOnDelete();
            $table->foreignId('patient_type_id')
                ->constrained('patient_types')
                ->cascadeOnDelete();
            $table->string('case_number')->unique(); // patient number
            $table->dateTime('admission_datetime');
            $table->string('chief_complaint');
            $table->string('initial_diagnosis')->nullable();
            $table->string('final_diagnosis')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_types');

        Schema::dropIfExists('buildings');
        Schema::dropIfExists('floors');
        Schema::dropIfExists('wards');
        Schema::dropIfExists('beds');
        Schema::dropIfExists('stations');
        Schema::dropIfExists('rooms');

        Schema::dropIfExists('patient_cases');
    }
};
