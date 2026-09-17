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
        Schema::create('lab_test_categories', function (Blueprint $table) {
            $table->id();
            $table->string('pid', 255)->unique();
            $table->string('name'); // e.g., Hematology, Microbiology
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('pid', 255)->unique();
            $table->foreignId('category_id')->constrained('lab_test_categories')->cascadeOnDelete();
            $table->string('code')->unique(); // e.g., CBC-01
            $table->string('name'); // e.g., Complete Blood Count
            $table->decimal('price', 10, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('lab_test_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('pid', 255)->unique();
            $table->foreignId('lab_test_id')->constrained('lab_tests')->cascadeOnDelete();
            $table->string('parameter_name'); // e.g., Hemoglobin
            $table->string('unit')->nullable(); // e.g., g/dL
            $table->string('reference_range')->nullable(); // e.g., 13.8 - 17.2
            $table->decimal('min_val', 8, 2)->nullable();
            $table->decimal('max_val', 8, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('lab_requests', function (Blueprint $table) {
            $table->id();
            $table->string('pid', 255)->unique();
            $table->string('request_number')->unique(); // e.g., LR-2026-0001
            $table->foreignId('patient_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'sample_collected', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('priority', ['routine', 'urgent', 'stat'])->default('routine');
            $table->text('clinical_notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('lab_samples', function (Blueprint $table) {
            $table->id();
            $table->string('pid', 255)->unique();
            $table->foreignId('lab_request_id')->constrained()->cascadeOnDelete();
            $table->string('barcode')->unique();
            $table->string('sample_type'); // e.g., Blood, Urine, Plasma
            $table->timestamp('collected_at')->nullable();
            $table->foreignId('collected_by')->nullable()->constrained('users');
            $table->enum('status', ['pending', 'collected', 'rejected', 'processed'])->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->string('pid', 255)->unique();
            $table->foreignId('lab_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parameter_id')->constrained('lab_test_parameters')->cascadeOnDelete();
            $table->string('result_value');
            $table->boolean('is_abnormal')->default(false);
            $table->foreignId('entered_by')->constrained('users');
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_test_categories');
        Schema::dropIfExists('lab_tests');
        Schema::dropIfExists('lab_test_parameters');
        Schema::dropIfExists('lab_requests');
        Schema::dropIfExists('lab_samples');
        Schema::dropIfExists('lab_results');
    }
};
