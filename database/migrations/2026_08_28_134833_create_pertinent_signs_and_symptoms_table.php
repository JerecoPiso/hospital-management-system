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
        Schema::create('pertinent_signs_and_symptoms', function (Blueprint $table) {
            $table->id();
            $table->string("pid")->unique();
            $table->foreignId('doctor_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string("values");
            $table->string("pain")->nullable();
            $table->string("others")->nullable();
            $table->string("remarks")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertinent_signs_and_symptoms');
    }
};
