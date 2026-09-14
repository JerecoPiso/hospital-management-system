<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RoleAccess extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['id', 'role_id', 'created_at', 'updated_at'];
    protected $casts = [
        'can_view' => 'boolean',
        'can_create' => 'boolean',
        'can_update' => 'boolean',
        'can_delete' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($roleAccess) {
            $roleAccess->pid = $roleAccess->pid ?? Str::uuid()->toString();
        });
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
