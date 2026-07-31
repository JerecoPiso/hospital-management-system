<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Supply extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'deleted_at', 'created_at', 'updated_at'];

    protected $casts = [
        'is_active' => 'boolean',
        'selling_price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supply) {
            $supply->pid = $supply->pid ?? Str::uuid()->toString();
        });
    }

    public function supplyStocks(): HasMany
    {
        return $this->hasMany(SupplyStock::class);
    }
}
