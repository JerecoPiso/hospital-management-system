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
        Schema::create('history_and_physical_examination_form_twos', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('general_appearance')->nullable();
            $table->string('general_appearance_others')->nullable();

            $table->string('skin')->nullable();
            $table->string('skin_others')->nullable();

            $table->string('heent')->nullable();
            $table->string('heent_others')->nullable();

            $table->string('neck')->nullable();
            $table->string('neck_others')->nullable();

            $table->string('chest_lungs')->nullable();
            $table->string('chest_lungs_others')->nullable();

            $table->string('cardiovascular')->nullable();
            $table->string('cardiovascular_others')->nullable();

            $table->string('abdomen')->nullable();
            $table->string('abdomen_others')->nullable();

            $table->string('genitourinary')->nullable();
            $table->string('genitourinary_others')->nullable();

            $table->string('rectal')->nullable();
            $table->string('rectal_others')->nullable();

            $table->string('musculoskeletal')->nullable();
            $table->string('musculoskeletal_others')->nullable();

            $table->string('neurological')->nullable();
            $table->string('neurological_others')->nullable();

            $table->string('psychiatric_mental_status')->nullable();
            $table->string('psychiatric_mental_status_others')->nullable();

            $table->string('assessment_impression')->nullable();
            $table->string('plan_recommendations')->nullable();

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
        Schema::dropIfExists('history_and_physical_examination_form_twos');
    }
};
