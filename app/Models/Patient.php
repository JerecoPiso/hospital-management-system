<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
class Patient extends Model
{
    //
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at', 'created_at', 'updated_at'];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($patient) {
            $patient->pid = $patient->pid ?? Str::uuid()->toString();
        });
    }
}
