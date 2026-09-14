<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * New modules aren't automatically visible to existing roles (no access row
     * means no access), so the Administrator role needs an explicit grant for
     * the 'supply-charges' module introduced alongside this feature.
     */
    public function up(): void
    {
        $roles = DB::table('roles')->where('name', 'Administrator')->get();
        $now = now();

        foreach ($roles as $role) {
            $exists = DB::table('role_accesses')
                ->where('role_id', $role->id)
                ->where('module', 'supply-charges')
                ->exists();

            if (!$exists) {
                DB::table('role_accesses')->insert([
                    'pid' => (string) Str::uuid(),
                    'role_id' => $role->id,
                    'module' => 'supply-charges',
                    'can_view' => true,
                    'can_create' => true,
                    'can_update' => true,
                    'can_delete' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('role_accesses')->where('module', 'supply-charges')->delete();
    }
};
