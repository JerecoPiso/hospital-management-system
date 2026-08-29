<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Icd extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'deleted_at', 'created_at', 'updated_at'];
    protected $casts = ['status' => 'boolean'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($icd) {
            $icd->pid = $icd->pid ?? Str::uuid()->toString();
        });
    }

    public function soaps(): HasMany
    {
        return $this->hasMany(Soap::class);
    }
}
