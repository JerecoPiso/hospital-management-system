<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            // Type of measurement
            $table->enum('type', ['opr', 'tpr', 'monitoring'])->default('opr');

            // Standard vitals
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('heart_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->integer('systolic')->nullable();
            $table->integer('diastolic')->nullable();
            $table->integer('oxygen_saturation')->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();

            // Optional adult/pediatric
            $table->decimal('bmi', 4, 1)->nullable();
            $table->decimal('muac', 5, 2)->nullable();
            $table->decimal('length', 5, 2)->nullable(); // pediatric
            $table->decimal('z_score', 4, 2)->nullable();
            $table->decimal('head_circumference', 5, 2)->nullable();
            $table->decimal('abdominal_circumference', 5, 2)->nullable();
            $table->decimal('chest_circumference', 5, 2)->nullable();
            // $table->decimal('waist', 5, 2)->nullable();
            // $table->decimal('hip', 5, 2)->nullable();
            // $table->decimal('waist_hip_ratio', 4, 2)->nullable();
            // $table->decimal('skinfold_thickness', 4, 2)->nullable();

            // Limb measurements
            // $table->decimal('right_arm', 5, 2)->nullable();
            // $table->decimal('left_arm', 5, 2)->nullable();
            // $table->decimal('right_leg', 5, 2)->nullable();
            // $table->decimal('left_leg', 5, 2)->nullable();

            // Vision
            // $table->decimal('right_vision', 3, 2)->nullable();
            // $table->decimal('left_vision', 3, 2)->nullable();

            // Obstetric-specific (if patient is maternal)
            $table->integer('fht')->nullable(); // fetal heart tone
            $table->date('lmp')->nullable();
            $table->integer('aog')->nullable(); // age of gestation in weeks
            $table->date('edc')->nullable(); // estimated date of confinement

            // GCS
            $table->integer('eye_response')->nullable();
            $table->integer('verbal_response')->nullable();
            $table->integer('motor_response')->nullable();


            $table->text('remarks')->nullable();
            $table->dateTime('measured_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vital_signs');
    }
};
