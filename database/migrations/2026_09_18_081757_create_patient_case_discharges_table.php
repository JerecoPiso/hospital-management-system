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
        Schema::create('patient_case_discharges', function (Blueprint $table) {
            $table->id();
            $table->string('pid', 100)->unique();

            $table->foreignId('patient_case_id')
                ->unique()
                ->constrained('patient_cases')
                ->cascadeOnDelete();

            $table->dateTime('discharge_datetime');

            $table->string('disposition');
            // Home, Transferred, DAMA, Absconded, Death, etc.

            $table->string('discharge_condition')->nullable();
            // Improved, Stable, Recovered, Unchanged, etc.

            $table->foreignId('discharged_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
                
            $table->softDeletes();
            $table->timestamps();
            $table->index(['discharge_datetime', 'disposition']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_case_discharges');
    }
};
