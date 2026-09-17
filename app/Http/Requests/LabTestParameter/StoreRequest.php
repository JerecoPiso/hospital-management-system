<?php

namespace App\Http\Requests\LabTestParameter;

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
            "lab_test_pid" => "required|string|exists:lab_tests,pid",
            "parameter_name" => "required|string",
            "unit" => "nullable|string",
            "reference_range" => "nullable|string",
            "min_val" => "nullable|numeric",
            "max_val" => "nullable|numeric",
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
