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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
        });

        // Seed a full-access "Administrator" role and attach every existing user to it
        // so nobody is locked out once permission middleware starts enforcing access.
        $roleId = DB::table('roles')->insertGetId([
            'pid' => (string) Str::uuid(),
            'name' => 'Administrator',
            'description' => 'Full access to every module in the system.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $modules = require __DIR__ . '/../../config/modules.php';
        $now = now();
        $rows = array_map(fn ($module) => [
            'pid' => (string) Str::uuid(),
            'role_id' => $roleId,
            'module' => $module['key'],
            'can_view' => true,
            'can_create' => true,
            'can_update' => true,
            'can_delete' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ], $modules);
        DB::table('role_accesses')->insert($rows);

        DB::table('users')->whereNull('role_id')->update(['role_id' => $roleId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });
    }
};
