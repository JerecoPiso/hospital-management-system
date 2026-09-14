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
        Schema::table('patients', function (Blueprint $table) {
            //
            $table->string('country', 100)->after('contact_number')->nullable();
            $table->string('region', 100)->after('country')->nullable();
            $table->string('province', 100)->after('region')->nullable();
            $table->string('municipality', 100)->after('province')->nullable();
            $table->string('barangay', 255)->after('municipality')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            //
            $table->dropColumn('country');
            $table->dropColumn('region');
            $table->dropColumn('province');
            $table->dropColumn('municipality');
            $table->dropColumn('barangay');
        });
    }
};
