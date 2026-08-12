<?php

namespace App\Http\Requests\Prescription;

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
            "prescription_date" => "required|date",
            "remarks" => "nullable|string",
            "status" => "nullable|in:requested,done,picked-up,cancelled",
            "items" => "required|array|min:1",
            "items.*.medicine_pid" => "required|string|exists:medicines,pid",
            "items.*.frequency" => "nullable|string",
            "items.*.duration" => "nullable|numeric",
            "items.*.duration_unit" => "nullable|string",
            "items.*.quantity" => "nullable|numeric",
            "items.*.instructions" => "nullable|string",
            "items.*.remarks" => "nullable|string",
            "items.*.status" => "nullable|string|max:25",
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
