<?php

namespace App\Http\Requests\PatientCaseBed;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            "patient_case_pid" => "required|string|exists:patient_cases,pid",
            "bed_pid" => "required|string|exists:beds,pid",
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date|after:started_at',
            "remarks" => "nullable|string"
        ];
    }
}
