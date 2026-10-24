<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HistoryAndPhysicalExaminationFormOne extends Model
{
    //
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'user_id', 'patient_case_id', 'deleted_at', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($history) {
            $history->pid = $history->pid ?? Str::uuid()->toString();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id', 'id');
    }

    public function patientCase(): BelongsTo
    {
        return $this->belongsTo(PatientCase::class);
    }

    public function findings(): BelongsToMany
    {
        return $this->belongsToMany(
            ExaminationOption::class,
            'history_and_physical_examination_form_one_findings',
            'history_and_physical_examination_form_one_id',
            'examination_option_id'
        )->using(HistoryAndPhysicalExaminationFormOneFinding::class)->withTrashed()->withTimestamps();
    }
}
