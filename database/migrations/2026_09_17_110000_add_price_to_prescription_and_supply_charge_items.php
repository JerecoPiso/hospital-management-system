<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Snapshots the item's unit price at the time it was prescribed/charged,
     * same rationale as fee_charge_items.unit_fee — later catalog price
     * changes shouldn't retroactively alter a patient's existing record.
     */
    public function up(): void
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0.00)->after('medicine_id');
        });

        Schema::table('supply_charge_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0.00)->after('supply_id');
        });
    }

    public function down(): void
    {
        Schema::table('prescription_items', function (Blueprint $table) {
            $table->dropColumn('price');
        });

        Schema::table('supply_charge_items', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
