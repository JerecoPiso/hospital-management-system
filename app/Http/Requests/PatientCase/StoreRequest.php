<?php

namespace App\Http\Requests\PatientCase;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "patient_pid" => "required|string|exists:patients,pid",
            "type" => "required|in:inpatient,outpatient",
            "admission_datetime" => "required|date",
            "chief_complaint" => "required|string",
            "initial_diagnosis" => "nullable|string",
            "final_diagnosis" => "nullable|string",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Invalid data.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
