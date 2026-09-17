<?php

namespace App\Http\Requests\RadiologyOrder;

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
            "status" => "required|in:ordered,scheduled,completed,cancelled",
            "scheduled_at" => "nullable|date",
            "performed_at" => "nullable|date",
            "technician_pid" => "nullable|string|exists:users,pid",
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
