<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MedicineDistribution extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'medicine_stock_id', 'station_id', 'distributed_by', 'deleted_at', 'created_at', 'updated_at'];

    protected $casts = [
        'distributed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($medicineDistribution) {
            $medicineDistribution->pid = $medicineDistribution->pid ?? Str::uuid()->toString();
        });
    }

    public function medicineStock(): BelongsTo
    {
        return $this->belongsTo(MedicineStock::class);
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function distributedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'distributed_by');
    }
}
