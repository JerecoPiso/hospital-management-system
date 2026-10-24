<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class HistoryAndPhysicalExaminationFormOneFinding extends Pivot
{
    //
    protected $table = 'history_and_physical_examination_form_one_findings';
    public $incrementing = true;

    protected $guarded = ['id'];
    protected $hidden = ['id', 'history_and_physical_examination_form_one_id', 'examination_option_id', 'created_at', 'updated_at'];

    public function form(): BelongsTo
    {
        return $this->belongsTo(HistoryAndPhysicalExaminationFormOne::class, 'history_and_physical_examination_form_one_id');
    }

    public function examinationOption(): BelongsTo
    {
        return $this->belongsTo(ExaminationOption::class)->withTrashed();
    }
}
