<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RadiologyReport extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'radiology_order_id', 'radiologist_id', 'deleted_at', 'created_at', 'updated_at'];
    protected $casts = ['finalized_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($report) {
            $report->pid = $report->pid ?? Str::uuid()->toString();
        });
    }

    public function radiologyOrder(): BelongsTo
    {
        return $this->belongsTo(RadiologyOrder::class);
    }

    public function radiologist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'radiologist_id');
    }
}
