<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Diet extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'deleted_at', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($diet) {
            $diet->pid = $diet->pid ?? Str::uuid()->toString();
        });
    }

    public function patientCaseDiets(): HasMany
    {
        return $this->hasMany(PatientCaseDiet::class);
    }
}
