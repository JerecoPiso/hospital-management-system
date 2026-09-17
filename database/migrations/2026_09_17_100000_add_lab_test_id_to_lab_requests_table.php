<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The original lab_requests table has no link to which catalog test is
     * being ordered. Adding it here so a request can be resolved to its test
     * (and, through it, the parameters that need results) — mirrors how
     * radiology_orders already points at a single procedure_id.
     */
    public function up(): void
    {
        Schema::table('lab_requests', function (Blueprint $table) {
            $table->foreignId('lab_test_id')->after('doctor_id')->constrained('lab_tests')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lab_requests', function (Blueprint $table) {
            $table->dropForeign(['lab_test_id']);
            $table->dropColumn('lab_test_id');
        });
    }
};
