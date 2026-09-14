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
        Schema::create('supply_charges', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();

            $table->foreignId('patient_case_id')->constrained('patient_cases')->cascadeOnDelete();
            $table->foreignId('charged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('charge_date');
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
        Schema::dropIfExists('supply_charges');
    }
};
