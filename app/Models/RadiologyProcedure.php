<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RadiologyProcedure extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'modality_id', 'deleted_at', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($procedure) {
            $procedure->pid = $procedure->pid ?? Str::uuid()->toString();
        });
    }

    public function modality(): BelongsTo
    {
        return $this->belongsTo(RadiologyModality::class, 'modality_id');
    }
}
