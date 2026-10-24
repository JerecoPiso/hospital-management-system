<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Selected findings now live in history_and_physical_examination_form_one_findings,
     * so the per-section free-text columns are redundant. The *_others columns stay.
     */
    private array $columns = [
        'past_medical_history',
        'past_surgical_history',
        'hospitalization_history',
        'medication_history',
        'allergies',
        'family_history',
        'social_history',
        'immunization_history',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('history_and_physical_examination_form_ones', function (Blueprint $table) {
            $table->dropColumn($this->columns);
            // Fix the misnamed "others" column so it matches the other sections
            $table->renameColumn('past_surgical_history_history', 'past_surgical_history_others');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('history_and_physical_examination_form_ones', function (Blueprint $table) {
            $table->renameColumn('past_surgical_history_others', 'past_surgical_history_history');
        });

        Schema::table('history_and_physical_examination_form_ones', function (Blueprint $table) {
            $previous = 'history_of_present_illness';
            foreach ($this->columns as $column) {
                $others = $column === 'past_surgical_history' ? 'past_surgical_history_history' : "{$column}_others";
                $table->string($column)->nullable()->after($previous);
                $previous = $others;
            }
        });
    }
};
