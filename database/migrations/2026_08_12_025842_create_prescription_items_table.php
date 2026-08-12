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
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->foreignId('prescription_id')
                ->constrained('prescriptions')
                ->cascadeOnDelete();
            $table->foreignId('medicine_id')
                ->constrained('medicines')
                ->restrictOnDelete();
            $table->string('frequency')->nullable();
            $table->decimal('duration', 8, 2)->nullable();
            $table->string('duration_unit')->nullable();
            $table->decimal('quantity', 12, 2)->nullable();
            $table->text('instructions')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status', 25)->default('continue')->index('prescription_items_idx_status');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
