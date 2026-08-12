<?php

namespace App\Http\Requests\PrescriptionItem;

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
            "frequency" => "nullable|string",
            "duration" => "nullable|numeric",
            "duration_unit" => "nullable|string",
            "quantity" => "nullable|numeric",
            "instructions" => "nullable|string",
            "remarks" => "nullable|string",
            "status" => "nullable|string|max:25",
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
