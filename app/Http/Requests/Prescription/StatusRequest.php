<?php

namespace App\Http\Requests\Prescription;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "status" => "required|in:requested,done,picked-up,cancelled",
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
