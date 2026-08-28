<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The original migration declared `others` via `$table->string('others')->nullable`
     * (missing parentheses), so the column was created NOT NULL. This makes it nullable.
     */
    public function up(): void
    {
        Schema::table('pertinent_signs_and_symptoms_lists', function (Blueprint $table) {
            $table->string('others')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pertinent_signs_and_symptoms_lists', function (Blueprint $table) {
            $table->string('others')->nullable(false)->default('')->change();
        });
    }
};
