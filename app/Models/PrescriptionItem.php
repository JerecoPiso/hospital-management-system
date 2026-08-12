<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PrescriptionItem extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'prescription_id', 'medicine_id', 'deleted_at', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->pid = $item->pid ?? Str::uuid()->toString();
        });
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}
