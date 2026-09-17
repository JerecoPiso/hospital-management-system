<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LabTestCategory extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'deleted_at', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->pid = $category->pid ?? Str::uuid()->toString();
        });
    }

    public function tests(): HasMany
    {
        return $this->hasMany(LabTest::class, 'category_id');
    }
}
