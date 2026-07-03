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
        Schema::create('history_and_physical_examination_form_ones', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('chief_complaint');
            $table->string('history_of_present_illness');
            $table->string('past_medical_history')->nullable();
            $table->string('past_medical_history_others')->nullable();

            $table->string('past_surgical_history')->nullable();
            $table->string('past_surgical_history_history')->nullable();

            $table->string('hospitalization_history')->nullable();
            $table->string('hospitalization_history_others')->nullable();

            $table->string('medication_history')->nullable();
            $table->string('medication_history_others')->nullable();

            $table->string('allergies')->nullable();
            $table->string('allergies_others')->nullable();

            $table->string('family_history')->nullable();
            $table->string('family_history_others')->nullable();

            $table->string('social_history')->nullable();
            $table->string('social_history_others')->nullable();

            $table->string('immunization_history')->nullable();
            $table->string('immunization_history_others')->nullable();

            $table->string('review_of_systems')->nullable();
            $table->text('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_and_physical_examination_form_ones');
    }
};
