<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LabResult extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'lab_request_id', 'parameter_id', 'entered_by', 'verified_by', 'deleted_at', 'created_at', 'updated_at'];
    protected $casts = ['is_abnormal' => 'boolean', 'verified_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($result) {
            $result->pid = $result->pid ?? Str::uuid()->toString();
        });
    }

    public function labRequest(): BelongsTo
    {
        return $this->belongsTo(LabRequest::class);
    }

    public function parameter(): BelongsTo
    {
        return $this->belongsTo(LabTestParameter::class, 'parameter_id');
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
