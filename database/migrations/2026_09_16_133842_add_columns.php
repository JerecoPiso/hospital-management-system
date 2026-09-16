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
        Schema::table('patient_cases', function (Blueprint $table) {
            //
            $table->string('od', 25)->after('final_diagnosis')->nullable();
            $table->string('os', 25)->after('od')->nullable();
            $table->string('ph_right', 25)->after('os')->nullable();
            $table->string('ph_left', 25)->after('ph_right')->nullable();
            $table->string('cc', 25)->after('ph_left')->nullable();
            $table->string('cc_od', 25)->after('cc')->nullable();
            $table->string('cc_os', 25)->after('cc_od')->nullable();
            $table->string('cc_ph_right', 25)->after('cc_os')->nullable();
            $table->string('cc_ph_left', 25)->after('cc_ph_right')->nullable();
            $table->string('iop', 25)->after('cc_ph_left')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_cases', function (Blueprint $table) {
            //
            $table->dropColumn([
                'od',
                'os',
                'ph_right',
                'ph_left',
                'cc',
                'cc_od',
                'cc_os',
                'cc_ph_right',
                'cc_ph_left',
                'iop',
            ]);
        });
    }
};
