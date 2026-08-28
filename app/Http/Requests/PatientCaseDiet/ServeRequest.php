<?php

namespace App\Http\Requests\PatientCaseDiet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ServeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "served_at" => "nullable|date",
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
