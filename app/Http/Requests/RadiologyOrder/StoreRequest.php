<?php

namespace App\Http\Requests\RadiologyOrder;

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
            "patient_case_pid" => "required|string|exists:patient_cases,pid",
            "procedure_pid" => "required|string|exists:radiology_procedures,pid",
            "priority" => "nullable|in:routine,urgent,stat",
            "clinical_history" => "nullable|string",
            "scheduled_at" => "nullable|date",
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
