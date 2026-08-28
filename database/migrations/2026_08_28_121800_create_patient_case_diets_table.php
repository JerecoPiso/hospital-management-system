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
        Schema::create('patient_case_diets', function (Blueprint $table) {
            $table->id();
            $table->string('pid', 255)->unique();
            $table->foreignId('patient_case_id')
                ->constrained('patient_cases')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->comment("The one who set the diet")
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('diet_id')
                ->constrained('diets')
                ->cascadeOnDelete();
            $table->string('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_case_diets');
    }
};
