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
        Schema::create('examination_options', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->enum('type', [
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
            ]);
            $table->string('code');
            $table->string('description');
            $table->boolean('active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['type', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examination_options');
    }
};
