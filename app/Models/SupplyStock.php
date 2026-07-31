<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SupplyStock extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'supply_id', 'deleted_at', 'created_at', 'updated_at'];

    protected $casts = [
        'expiration_date' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplyStock) {
            $supplyStock->pid = $supplyStock->pid ?? Str::uuid()->toString();
        });
    }

    public function supply(): BelongsTo
    {
        return $this->belongsTo(Supply::class);
    }

    public function supplyMovements(): HasMany
    {
        return $this->hasMany(SupplyMovement::class);
    }

    public function supplyDistributions(): HasMany
    {
        return $this->hasMany(SupplyDistribution::class);
    }
}
