<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DoctorFee extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'patient_case_id', 'doctor_id', 'added_by', 'deleted_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($fee) {
            $fee->pid = $fee->pid ?? Str::uuid()->toString();
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

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function invoiceItem(): MorphOne
    {
        return $this->morphOne(InvoiceItem::class, 'billable');
    }
}
