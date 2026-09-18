<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Links a stock movement back to the supply charge item that caused it,
     * so updating/deleting a supply charge can reverse the exact batches it
     * drew from instead of guessing.
     */
    public function up(): void
    {
        Schema::table('supply_movements', function (Blueprint $table) {
            $table->foreignId('supply_charge_item_id')->nullable()->after('supply_stock_id')
                ->constrained('supply_charge_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('supply_movements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supply_charge_item_id');
        });
    }
};
