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
        Schema::create('history_and_physical_examination_form_two_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('history_and_physical_examination_form_two_id')
                ->constrained('history_and_physical_examination_form_twos', 'id', 'hpe_form_two_findings_form_fk')
                ->cascadeOnDelete();
            $table->foreignId('examination_option_id')
                ->constrained('examination_options', 'id', 'hpe_form_two_findings_option_fk')
                ->restrictOnDelete();
            $table->timestamps();

            $table->unique(['history_and_physical_examination_form_two_id', 'examination_option_id'], 'hpe_form_two_findings_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_and_physical_examination_form_two_findings');
    }
};
