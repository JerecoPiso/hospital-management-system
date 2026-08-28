<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The original create_diets_served migration declared `remarks` via
     * `$table->softDeletes('remarks')`, which produced a nullable TIMESTAMP
     * column instead of a text column. This corrects it and adds an explicit
     * `served_at` timestamp for when the diet was actually served.
     */
    public function up(): void
    {
        Schema::table('diets_served', function (Blueprint $table) {
            $table->string('remarks')->nullable()->change();

            if (!Schema::hasColumn('diets_served', 'served_at')) {
                $table->timestamp('served_at')->nullable()->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diets_served', function (Blueprint $table) {
            if (Schema::hasColumn('diets_served', 'served_at')) {
                $table->dropColumn('served_at');
            }

            $table->timestamp('remarks')->nullable()->change();
        });
    }
};
