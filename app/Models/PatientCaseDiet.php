<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PatientCaseDiet extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'user_id', 'patient_case_id', 'diet_id', 'deleted_at', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($diet) {
            $diet->pid = $diet->pid ?? Str::uuid()->toString();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patientCase(): BelongsTo
    {
        return $this->belongsTo(PatientCase::class);
    }

    public function diet(): BelongsTo
    {
        return $this->belongsTo(Diet::class);
    }

    public function dietsServed(): HasMany
    {
        return $this->hasMany(DietsServed::class);
    }
}
