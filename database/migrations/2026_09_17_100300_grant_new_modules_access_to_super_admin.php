<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * The earlier grant migration targeted a role named 'Administrator', which
     * doesn't exist in this database — the actual top-level role here is
     * 'Super Admin'. This corrects the grant so the 3 patient-workflow modules
     * introduced alongside lab/radiology/fees are visible without requiring a
     * manual re-save of the Roles & Permissions screen.
     */
    private array $modules = [
        'lab-test-categories',
        'lab-tests',
        'lab-test-parameters',
        'radiology-modalities',
        'radiology-procedures',
        'fee-categories',
        'fee-schedules',
        'lab-requests',
        'radiology-orders',
        'fee-charges',
    ];

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
        // Intentionally left as a no-op: reversing would also delete grants the
        // Roles screen may have since saved normally for these modules.
    }
};
