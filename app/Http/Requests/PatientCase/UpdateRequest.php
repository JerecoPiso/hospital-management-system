<?php

namespace App\Http\Requests\PatientCase;

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
            "admission_datetime" => "required|date",
            "chief_complaint" => "required|string",
            "initial_diagnosis" => "nullable|string",
            "final_diagnosis" => "nullable|string",
            "od" => "nullable|string|max:25",
            "os" => "nullable|string|max:25",
            "ph_right" => "nullable|string|max:25",
            "ph_left" => "nullable|string|max:25",
            "cc" => "nullable|string|max:25",
            "cc_od" => "nullable|string|max:25",
            "cc_os" => "nullable|string|max:25",
            "cc_ph_right" => "nullable|string|max:25",
            "cc_ph_left" => "nullable|string|max:25",
            "iop" => "nullable|string|max:25",
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
