<?php

namespace App\Http\Requests\FeeCharge;

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
            "charge_date" => "required|date",
            "remarks" => "nullable|string",
            "items" => "required|array|min:1",
            "items.*.fee_schedule_pid" => "required|string|exists:fee_schedules,pid",
            "items.*.quantity" => "required|numeric|min:0.01",
            "items.*.remarks" => "nullable|string",
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
