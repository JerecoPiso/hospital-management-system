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
        Schema::create('medicine_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();

            // $table->foreignId('medicine_stock_id')->constrained()->cascadeOnDelete();

            $table->foreignId('medicine_stock_id');

            $table->foreign('medicine_stock_id', 'medicine_stock_movements_medicine_stock_id_foreign')
                ->references('id')
                ->on('medicine_stocks')
                ->cascadeOnDelete();
            $table->enum('type', ['IN', 'OUT']); // delivery or dispense
            $table->integer('quantity');

            $table->string('reference')->nullable(); // prescription #, delivery #, etc.
            $table->text('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['medicine_stock_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_stock_movements');
    }
};
