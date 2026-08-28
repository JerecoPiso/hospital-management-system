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
        Schema::create('diets_served', function (Blueprint $table) {
            $table->id();
            $table->string('pid', 255)->unique();
            $table->foreignId('patient_case_diet_id')
                ->constrained('patient_case_diets')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->comment("The one who give the food")
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamp('served_at')->nullable();
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
        Schema::dropIfExists('diets_served');
    }
};
