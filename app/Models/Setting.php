<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Setting extends Model
{
    use SoftDeletes;

    /**
     * Default settings the application relies on. Their names are locked and they cannot be deleted.
     */
    public const DEFAULTS = [
        'professional_fee' => '800',
        'expiry_day' => '30',
    ];

    protected $guarded = ['id'];
    protected $hidden = ['id', 'deleted_at', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($setting) {
            $setting->pid = $setting->pid ?? Str::uuid()->toString();
        });
    }
}
