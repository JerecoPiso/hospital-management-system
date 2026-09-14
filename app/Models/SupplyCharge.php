<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SupplyCharge extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'patient_case_id', 'charged_by', 'deleted_at', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplyCharge) {
            $supplyCharge->pid = $supplyCharge->pid ?? Str::uuid()->toString();
        });
    }

    public function patientCase(): BelongsTo
    {
        return $this->belongsTo(PatientCase::class);
    }

    public function chargedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'charged_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SupplyChargeItem::class);
    }
}
