<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Role extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'deleted_at', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($role) {
            $role->pid = $role->pid ?? Str::uuid()->toString();
        });
    }

    public function roleAccesses(): HasMany
    {
        return $this->hasMany(RoleAccess::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
