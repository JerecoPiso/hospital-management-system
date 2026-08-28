<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The pertinent signs & symptoms form is filled per patient case, so it
     * needs a patient_case_id link (missing from the original create migration).
     */
    public function up(): void
    {
        Schema::table('pertinent_signs_and_symptoms', function (Blueprint $table) {
            if (!Schema::hasColumn('pertinent_signs_and_symptoms', 'patient_case_id')) {
                $table->foreignId('patient_case_id')
                    ->after('pid')
                    ->constrained('patient_cases', 'id', 'pertinent_signs_patient_case_fk')
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pertinent_signs_and_symptoms', function (Blueprint $table) {
            if (Schema::hasColumn('pertinent_signs_and_symptoms', 'patient_case_id')) {
                $table->dropForeign('pertinent_signs_patient_case_fk');
                $table->dropColumn('patient_case_id');
            }
        });
    }
};
