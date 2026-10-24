<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ExaminationOption extends Model
{
    //
    use SoftDeletes;

    const FORM_ONE_TYPES = [
        'past_medical_history',
        'past_surgical_history',
        'hospitalization_history',
        'medication_history',
        'allergies',
        'family_history',
        'social_history',
        'immunization_history',
    ];

    const FORM_TWO_TYPES = [
        'general_appearance',
        'skin',
        'heent',
        'neck',
        'chest_lungs',
        'cardiovascular',
        'abdomen',
        'genitourinary',
        'rectal',
        'musculoskeletal',
        'neurological',
        'psychiatric_mental_status',
    ];

    const TYPES = [...self::FORM_ONE_TYPES, ...self::FORM_TWO_TYPES];

    protected $guarded = ['id'];
    protected $hidden = ['id', 'deleted_at', 'created_at', 'updated_at', 'pivot'];
    protected $casts = ['active' => 'boolean'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($option) {
            $option->pid = $option->pid ?? Str::uuid()->toString();
        });
    }
}
