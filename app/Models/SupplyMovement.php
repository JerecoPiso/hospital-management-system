<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SupplyMovement extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'supply_stock_id', 'supply_charge_item_id', 'deleted_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplyMovement) {
            $supplyMovement->pid = $supplyMovement->pid ?? Str::uuid()->toString();
        });
    }

    public function supplyStock(): BelongsTo
    {
        return $this->belongsTo(SupplyStock::class);
    }

    public function supplyChargeItem(): BelongsTo
    {
        return $this->belongsTo(SupplyChargeItem::class);
    }
}
