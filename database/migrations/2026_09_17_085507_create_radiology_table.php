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
        Schema::create('radiology_modalities', function (Blueprint $table) {
            $table->id();
            $table->string('pid', 255)->unique();
            $table->string('code')->unique(); // e.g., XR, MRI, CT, US, ECHO
            $table->string('name'); // e.g., X-Ray, 2D Echocardiography
            $table->string('room_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('radiology_procedures', function (Blueprint $table) {
            $table->id();
            $table->string('pid', 255)->unique();
            $table->foreignId('modality_id')->constrained('radiology_modalities')->cascadeOnDelete();
            $table->string('code')->unique(); // e.g., RAD-CT-001
            $table->string('name'); // e.g., CT Scan Head, 2D Echo with Doppler
            $table->string('body_part')->nullable(); // e.g., Head, Chest, Heart
            $table->decimal('price', 10, 2)->default(0.00);
            $table->integer('estimated_duration_minutes')->default(30);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('radiology_orders', function (Blueprint $table) {
            $table->id();
            $table->string('pid', 255)->unique();
            $table->string('order_number')->unique(); // e.g., RAD-2026-0001
            $table->foreignId('patient_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users');
            $table->foreignId('procedure_id')->constrained('radiology_procedures');
            $table->enum('status', ['ordered', 'scheduled', 'completed', 'cancelled'])->default('ordered');
            $table->enum('priority', ['routine', 'urgent', 'stat'])->default('routine');
            $table->text('clinical_history')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('performed_at')->nullable();
            $table->foreignId('technician_id')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('radiology_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('pid', 255)->unique();
            $table->foreignId('radiology_order_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable(); // e.g., image/jpeg, application/pdf
            $table->integer('file_size')->nullable(); // In bytes
            $table->foreignId('uploaded_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('radiology_reports', function (Blueprint $table) {
            $table->id();
            $table->string('pid', 255)->unique();
            $table->foreignId('radiology_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('radiologist_id')->constrained('users');
            $table->longText('findings');
            $table->text('impression');
            $table->enum('status', ['draft', 'finalized', 'amended'])->default('draft');
            $table->timestamp('finalized_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radiology_modalities');
        Schema::dropIfExists('radiology_procedures');
        Schema::dropIfExists('radiology_orders');
        Schema::dropIfExists('radiology_attachments');
        Schema::dropIfExists('radiology_reports');
    }
};
