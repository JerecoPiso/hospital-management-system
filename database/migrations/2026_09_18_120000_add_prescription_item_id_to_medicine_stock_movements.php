<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Links a stock movement back to the prescription item that caused it,
     * so deleting/updating a prescription can reverse the exact batches it
     * drew from instead of guessing.
     */
    public function up(): void
    {
        Schema::table('medicine_stock_movements', function (Blueprint $table) {
            $table->foreignId('prescription_item_id')->nullable()->after('medicine_stock_id')
                ->constrained('prescription_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('medicine_stock_movements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('prescription_item_id');
        });
    }
};
