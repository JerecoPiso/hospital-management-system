<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LabTest extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'category_id', 'deleted_at', 'created_at', 'updated_at'];
    protected $casts = ['is_active' => 'boolean'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($test) {
            $test->pid = $test->pid ?? Str::uuid()->toString();
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LabTestCategory::class, 'category_id');
    }

    public function parameters(): HasMany
    {
        return $this->hasMany(LabTestParameter::class);
    }
}
