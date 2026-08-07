<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MedicineStock extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'medicine_id', 'deleted_at', 'created_at', 'updated_at'];

    protected $casts = [
        'expiration_date' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($medicineStock) {
            $medicineStock->pid = $medicineStock->pid ?? Str::uuid()->toString();
        });
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function medicineDistributions(): HasMany
    {
        return $this->hasMany(MedicineDistribution::class);
    }
}
