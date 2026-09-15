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
        Schema::table('medicine_stock_movements', function (Blueprint $table) {
            $table->dropForeign(['medicine_stock_id']);
            $table->dropColumn('medicine_stock_id');
            $table->foreignId('medicine_stock_id')
                ->after('pid')
                ->constrained('medicine_stocks')
                ->cascadeOnDelete();
                
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine_stock_movements', function (Blueprint $table) {
            $table->dropForeign(['medicine_stock_id']);
            $table->dropColumn('medicine_stock_id');
            $table->foreignId('medicine_id')
                ->constrained('medicines')
                ->cascadeOnDelete();
        });
    }
};
