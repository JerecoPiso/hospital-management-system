<?php

namespace App\Http\Requests\LabRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResultsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "results" => "required|array|min:1",
            "results.*.parameter_pid" => "required|string|exists:lab_test_parameters,pid",
            "results.*.result_value" => "required|string",
            "results.*.is_abnormal" => "nullable|boolean",
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
