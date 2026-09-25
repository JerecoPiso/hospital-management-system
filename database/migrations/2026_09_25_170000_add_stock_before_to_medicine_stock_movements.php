<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Snapshot of the medicine's total on-hand quantity (all batches) just
     * before the movement, so reports can show historical stock levels without
     * reconstructing them. Nullable: rows created before this column have none.
     */
    public function up(): void
    {
        Schema::table('medicine_stock_movements', function (Blueprint $table) {
            $table->integer('stock_before')->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('medicine_stock_movements', function (Blueprint $table) {
            $table->dropColumn('stock_before');
        });
    }
};
