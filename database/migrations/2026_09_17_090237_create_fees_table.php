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
        Schema::create('fee_categories', function (Blueprint $table) {
            $table->id();
            $table->string('pid', 255)->unique();
            $table->string('name'); // e.g., Minor Operations, Consultations, Nursing Care
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('fee_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('pid', 255)->unique();
            $table->foreignId('fee_category_id')->constrained('fee_categories')->cascadeOnDelete();
            $table->string('code')->unique(); // e.g., SURG-MIN-001, CONSULT-GEN
            $table->string('name'); // e.g., Wound Suture / Laceration Repair, Circumcision
            $table->decimal('standard_fee', 10, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_categories');
        Schema::dropIfExists('fee_schedules');
    }
};
