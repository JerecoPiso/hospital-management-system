<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Snapshots the test/procedure's catalog price at the time it was
     * ordered — same rationale as fee_charge_items.unit_fee and the price
     * columns added to prescription_items / supply_charge_items — later
     * catalog price changes shouldn't retroactively alter an existing order.
     */
    public function up(): void
    {
        Schema::table('lab_requests', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0.00)->after('lab_test_id');
        });

        Schema::table('radiology_orders', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0.00)->after('procedure_id');
        });
    }

    public function down(): void
    {
        Schema::table('lab_requests', function (Blueprint $table) {
            $table->dropColumn('price');
        });

        Schema::table('radiology_orders', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
