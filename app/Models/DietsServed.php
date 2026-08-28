<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DietsServed extends Model
{
    use SoftDeletes;

    protected $table = 'diets_served';
    protected $guarded = ['id'];
    protected $hidden = ['id', 'user_id', 'patient_case_diet_id', 'deleted_at', 'created_at', 'updated_at'];
    protected $casts = [
        'served_at' => 'datetime',
    ];

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

    public function patientCaseDiet(): BelongsTo
    {
        return $this->belongsTo(PatientCaseDiet::class);
    }
}
