<?php

namespace App\Http\Requests\PertinentSignsAndSymptoms;

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
            "values" => "nullable|string",   // selected list codes joined by ";" e.g. "1;4;5;X"
            "pain" => "nullable|string|max:255",
            "others" => "nullable|string|max:255",
            "remarks" => "nullable|string|max:255",
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
