<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class HistoryAndPhysicalExaminationFormTwoFinding extends Pivot
{
    //
    protected $table = 'history_and_physical_examination_form_two_findings';
    public $incrementing = true;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'history_and_physical_examination_form_two_id', 'examination_option_id', 'created_at', 'updated_at'];

    public function form(): BelongsTo
    {
        return $this->belongsTo(HistoryAndPhysicalExaminationFormTwo::class, 'history_and_physical_examination_form_two_id');
    }

    public function examinationOption(): BelongsTo
    {
        return $this->belongsTo(ExaminationOption::class)->withTrashed();
    }
}
