<?php

namespace App\Http\Requests\RadiologyProcedure;

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
            "modality_pid" => "required|string|exists:radiology_modalities,pid",
            "code" => "required|string",
            "name" => "required|string",
            "body_part" => "nullable|string",
            "price" => "required|numeric",
            "estimated_duration_minutes" => "nullable|integer|min:1",
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
