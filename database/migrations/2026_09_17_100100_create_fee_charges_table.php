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
        Schema::create('fee_charges', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();

            $table->foreignId('patient_case_id')->constrained('patient_cases')->cascadeOnDelete();
            $table->foreignId('charged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('charge_date');
            $table->text('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('fee_charge_items', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();

            $table->foreignId('fee_charge_id')->constrained('fee_charges')->cascadeOnDelete();
            $table->foreignId('fee_schedule_id')->constrained('fee_schedules')->restrictOnDelete();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_fee', 10, 2)->default(0.00);
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
        Schema::dropIfExists('fee_charge_items');
        Schema::dropIfExists('fee_charges');
    }
};
