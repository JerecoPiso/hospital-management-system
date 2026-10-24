<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Selected findings now live in history_and_physical_examination_form_two_findings,
     * so the per-system free-text columns are redundant. The *_others columns stay.
     */
    private array $columns = [
        'general_appearance',
        'skin',
        'heent',
        'neck',
        'chest_lungs',
        'cardiovascular',
        'abdomen',
        'genitourinary',
        'rectal',
        'musculoskeletal',
        'neurological',
        'psychiatric_mental_status',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('history_and_physical_examination_form_twos', function (Blueprint $table) {
            $table->dropColumn($this->columns);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('history_and_physical_examination_form_twos', function (Blueprint $table) {
            $previous = 'doctor_id';
            foreach ($this->columns as $column) {
                $table->string($column)->nullable()->after($previous);
                $previous = "{$column}_others";
            }
        });
    }
};
