<?php

namespace App\Http\Requests\PatientCaseDiet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "patient_case_pid" => "nullable|string|exists:patient_cases,pid",
            "diet_pid" => "nullable|string|exists:diets,pid",
            "remarks" => "nullable|string",
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
