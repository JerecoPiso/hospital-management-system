<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->string('name')->unique();
            $table->string('value');
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Default settings
        DB::table('settings')->insert([
            ['pid' => Str::uuid()->toString(), 'name' => 'professional_fee', 'value' => '800', 'description' => 'Default professional fee amount', 'created_at' => now(), 'updated_at' => now()],
            ['pid' => Str::uuid()->toString(), 'name' => 'expiry_day', 'value' => '30', 'description' => 'Number of days before expiry', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
