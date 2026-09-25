<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 2026_09_14_050536_add_price_column is recorded as run, but some databases
 * ended up without the column, which breaks the movements list (income total)
 * and prescription dispensing. Re-add it only where it is missing.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('medicine_stock_movements', 'price')) {
            return;
        }

        Schema::table('medicine_stock_movements', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->after('medicine_stock_id')->default(0);
        });
    }

    public function down(): void
    {
        // Intentionally left empty: the column belongs to 2026_09_14_050536_add_price_column.
    }
};
