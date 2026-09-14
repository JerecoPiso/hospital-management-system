<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SupplyChargeItem extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'supply_charge_id', 'supply_id', 'deleted_at', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->pid = $item->pid ?? Str::uuid()->toString();
        });
    }

    public function supplyCharge(): BelongsTo
    {
        return $this->belongsTo(SupplyCharge::class);
    }

    public function supply(): BelongsTo
    {
        return $this->belongsTo(Supply::class);
    }
}
