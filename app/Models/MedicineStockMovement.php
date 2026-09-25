<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MedicineStockMovement extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'medicine_id', 'prescription_item_id', 'deleted_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($medicineStockMovement) {
            $medicineStockMovement->pid = $medicineStockMovement->pid ?? Str::uuid()->toString();
            $medicineStockMovement->stock_before ??= static::stockBeforeFor($medicineStockMovement);
        });
    }

    /**
     * Every caller saves the batch's new quantity before creating its movement,
     * so the medicine's current total already includes this movement; undo it
     * to get the total just before. For a dispense spanning several batches,
     * the first movement therefore holds the stock before the whole dispense.
     */
    private static function stockBeforeFor(self $movement): ?int
    {
        $medicineId = MedicineStock::withTrashed()->whereKey($movement->medicine_stock_id)->value('medicine_id');
        if (!$medicineId) {
            return null;
        }

        $currentTotal = (int) MedicineStock::where('medicine_id', $medicineId)->sum('quantity');
        $quantity = (int) $movement->quantity;

        return $movement->type === 'IN' ? $currentTotal - $quantity : $currentTotal + $quantity;
    }

    public function medicineStock(): BelongsTo
    {
        return $this->belongsTo(MedicineStock::class);
    }

    public function prescriptionItem(): BelongsTo
    {
        return $this->belongsTo(PrescriptionItem::class);
    }
}
