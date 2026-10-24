<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $formTwoTypes = [
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

    private array $formOneTypes = [
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
        Schema::table('examination_options', function (Blueprint $table) {
            $table->enum('type', array_merge($this->formOneTypes, $this->formTwoTypes))->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('examination_options')->whereIn('type', $this->formOneTypes)->delete();

        Schema::table('examination_options', function (Blueprint $table) {
            $table->enum('type', $this->formTwoTypes)->change();
        });
    }
};
