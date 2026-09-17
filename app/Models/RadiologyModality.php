<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RadiologyModality extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'deleted_at', 'created_at', 'updated_at'];
    protected $casts = ['is_active' => 'boolean'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($modality) {
            $modality->pid = $modality->pid ?? Str::uuid()->toString();
        });
    }

    public function procedures(): HasMany
    {
        return $this->hasMany(RadiologyProcedure::class, 'modality_id');
    }
}
