<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DoctorsOrder extends Model
{
    //
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'user_id', 'deleted_at', 'created_at', 'updated_at'];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->pid = $order->pid ?? Str::uuid()->toString();
        });
    }

    public function user(): BelongsTo
    {
        return  $this->belongsTo(User::class);
    }
}
