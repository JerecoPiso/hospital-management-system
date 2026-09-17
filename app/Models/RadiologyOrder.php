<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RadiologyOrder extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'patient_case_id', 'doctor_id', 'procedure_id', 'technician_id', 'deleted_at', 'created_at', 'updated_at'];
    protected $casts = ['scheduled_at' => 'datetime', 'performed_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->pid = $order->pid ?? Str::uuid()->toString();
        });
    }

    public function patientCase(): BelongsTo
    {
        return $this->belongsTo(PatientCase::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(RadiologyProcedure::class, 'procedure_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function report(): HasOne
    {
        return $this->hasOne(RadiologyReport::class);
    }
}
