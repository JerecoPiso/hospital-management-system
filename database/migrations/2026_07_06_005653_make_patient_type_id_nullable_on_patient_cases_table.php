<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patient_cases', function (Blueprint $table) {
            $table->dropForeign(['patient_type_id']);
        });
        DB::statement('ALTER TABLE patient_cases MODIFY patient_type_id BIGINT UNSIGNED NULL');
        Schema::table('patient_cases', function (Blueprint $table) {
            $table->foreign('patient_type_id')->references('id')->on('patient_types')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_cases', function (Blueprint $table) {
            $table->dropForeign(['patient_type_id']);
        });
        DB::statement('ALTER TABLE patient_cases MODIFY patient_type_id BIGINT UNSIGNED NOT NULL');
        Schema::table('patient_cases', function (Blueprint $table) {
            $table->foreign('patient_type_id')->references('id')->on('patient_types')->cascadeOnDelete();
        });
    }
};
