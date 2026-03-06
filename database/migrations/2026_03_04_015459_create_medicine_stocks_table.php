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
        Schema::create('medicine_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();

            $table->foreignId('medicine_id')
                ->constrained('medicines')
                ->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->decimal('purchase_price', 12, 2);
            $table->integer('reorder_level')->default(100);
            $table->string('unit_type')->default('box');          // box, roll, pack, bottle, etc.
            $table->integer('units_per_package')->default(1);    // pieces per box/roll
            $table->date('expiration_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['medicine_id', 'expiration_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_stocks');
    }
};
