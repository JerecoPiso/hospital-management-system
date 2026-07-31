<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('ALTER TABLE beds ADD COLUMN pid VARCHAR(255) NULL AFTER id');

        DB::table('beds')->whereNull('pid')->orderBy('id')->get()->each(function ($bed) {
            DB::table('beds')->where('id', $bed->id)->update(['pid' => (string) Str::uuid()]);
        });

        DB::statement('ALTER TABLE beds MODIFY COLUMN pid VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE beds ADD UNIQUE (pid)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE beds DROP INDEX pid');
        DB::statement('ALTER TABLE beds DROP COLUMN pid');
    }
};
