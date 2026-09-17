<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * New modules aren't automatically visible to existing roles (no access row
     * means no access), so the top-level roles need an explicit grant for the
     * 'invoices' module introduced alongside the billing feature.
     */
    private array $modules = ['invoices'];

    public function up(): void
    {
        $roles = DB::table('roles')->whereIn('name', ['Super Admin', 'Administrator'])->get();
        $now = now();

        foreach ($roles as $role) {
            foreach ($this->modules as $module) {
                $exists = DB::table('role_accesses')
                    ->where('role_id', $role->id)
                    ->where('module', $module)
                    ->exists();

                if (!$exists) {
                    DB::table('role_accesses')->insert([
                        'pid' => (string) Str::uuid(),
                        'role_id' => $role->id,
                        'module' => $module,
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('role_accesses')->whereIn('module', $this->modules)->delete();
    }
};
