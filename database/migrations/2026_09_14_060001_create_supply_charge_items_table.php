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
        Schema::create('supply_charge_items', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();

            $table->foreignId('supply_charge_id')->constrained('supply_charges')->cascadeOnDelete();
            $table->foreignId('supply_id')->constrained('supplies')->restrictOnDelete();
            $table->decimal('quantity', 12, 2);
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
        Schema::dropIfExists('supply_charge_items');
    }
};
