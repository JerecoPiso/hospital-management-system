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
        Schema::create('medicine_distributions', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();

            $table->foreignId('medicine_stock_id')->constrained('medicine_stocks')->cascadeOnDelete();
            $table->foreignId('station_id')->constrained('stations')->cascadeOnDelete();
            $table->integer('quantity');             // total in smallest unit (tablet/ml)
            $table->foreignId('distributed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('distributed_at')->default(now());
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_distributions');
    }
};
