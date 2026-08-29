<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Soap extends Model
{
    use SoftDeletes;

    protected $table = 'soaps';
    protected $guarded = ['id'];
    protected $hidden = ['id', 'patient_case_id', 'doctor_id', 'icd_id', 'deleted_at', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($soap) {
            $soap->pid = $soap->pid ?? Str::uuid()->toString();
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

    public function icd(): BelongsTo
    {
        return $this->belongsTo(Icd::class);
    }
}
