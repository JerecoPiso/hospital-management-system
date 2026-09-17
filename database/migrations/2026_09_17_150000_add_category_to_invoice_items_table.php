<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Maps each billable source to the category label shown on the printed
     * invoice and the legacy "<label> - " description prefix used before
     * this column existed, so existing rows can be normalized in one pass.
     */
    private array $categories = [
        'App\\Models\\LabRequest' => ['category' => 'Laboratory', 'prefix' => 'Lab Test - '],
        'App\\Models\\RadiologyOrder' => ['category' => 'Radiology', 'prefix' => 'Radiology - '],
        'App\\Models\\FeeChargeItem' => ['category' => 'Fee', 'prefix' => 'Fee - '],
        'App\\Models\\PrescriptionItem' => ['category' => 'Medicine', 'prefix' => 'Medicine - '],
        'App\\Models\\SupplyChargeItem' => ['category' => 'Supply', 'prefix' => 'Supply - '],
    ];

    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->string('category', 50)->nullable()->after('description');
        });

        foreach ($this->categories as $type => $info) {
            DB::table('invoice_items')->where('billable_type', $type)->update(['category' => $info['category']]);

            DB::table('invoice_items')
                ->where('billable_type', $type)
                ->where('description', 'like', $info['prefix'] . '%')
                ->get()
                ->each(function ($row) use ($info) {
                    DB::table('invoice_items')->where('id', $row->id)->update([
                        'description' => substr($row->description, strlen($info['prefix'])),
                    ]);
                });
        }

        DB::table('invoice_items')->whereNull('category')->update(['category' => 'Other']);
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
