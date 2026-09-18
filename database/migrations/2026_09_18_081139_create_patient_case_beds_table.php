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
        Schema::create('patient_case_beds', function (Blueprint $table) {
            $table->id();
            $table->string('pid', 100)->unique();
            $table->foreignId('patient_case_id')
                ->constrained('patient_cases')
                ->cascadeOnDelete();

            $table->foreignId('bed_id')
                ->constrained('beds')
                ->restrictOnDelete();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['patient_case_id', 'started_at']);
            $table->index(['bed_id', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_case_beds');
    }
};
