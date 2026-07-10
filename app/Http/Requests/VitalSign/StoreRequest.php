<?php

namespace App\Http\Requests\VitalSign;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            "type" => "nullable|in:opr,tpr,monitoring",
            "temperature" => "nullable|numeric",
            "heart_rate" => "nullable|integer",
            "respiratory_rate" => "nullable|integer",
            "systolic" => "nullable|integer",
            "diastolic" => "nullable|integer",
            "oxygen_saturation" => "nullable|integer",
            "weight" => "nullable|numeric",
            "height" => "nullable|numeric",
            "bmi" => "nullable|numeric",
            "muac" => "nullable|numeric",
            "length" => "nullable|numeric",
            "z_score" => "nullable|numeric",
            "head_circumference" => "nullable|numeric",
            "abdominal_circumference" => "nullable|numeric",
            "chest_circumference" => "nullable|numeric",
            "fht" => "nullable|integer",
            "lmp" => "nullable|date",
            "aog" => "nullable|integer",
            "edc" => "nullable|date",
            "eye_response" => "nullable|integer",
            "verbal_response" => "nullable|integer",
            "motor_response" => "nullable|integer",
            "remarks" => "nullable|string",
            "measured_at" => "nullable|date",
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
