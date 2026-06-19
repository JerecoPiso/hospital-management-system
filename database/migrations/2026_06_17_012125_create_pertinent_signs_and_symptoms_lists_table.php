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
        Schema::create('pertinent_signs_and_symptoms_lists', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->string('name', 100);
            $table->string('code', 10)->index('pertinent_signs_and_symptoms_lists_code_idx');
            $table->boolean('status')->default(true);
            $table->string('others')->nullable;
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertinent_signs_and_symptoms_lists');
    }
};
