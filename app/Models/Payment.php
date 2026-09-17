<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Payment extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'invoice_id', 'received_by', 'deleted_at', 'created_at', 'updated_at'];
    protected $casts = ['paid_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            $payment->pid = $payment->pid ?? Str::uuid()->toString();
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
