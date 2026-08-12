<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Prescription extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'patient_case_id', 'doctor_id', 'deleted_at', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($prescription) {
            $prescription->pid = $prescription->pid ?? Str::uuid()->toString();
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

    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}
