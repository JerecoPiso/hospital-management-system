<?php

namespace App\Http\Requests\HistoryAndPhysicalExaminationFormOne;

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
            "chief_complaint" => "required|string",
            "history_of_present_illness" => "required|string",
            "past_medical_history" => "nullable|string",
            "past_medical_history_others" => "nullable|string",
            "past_surgical_history" => "nullable|string",
            "past_surgical_history_history" => "nullable|string",
            "hospitalization_history" => "nullable|string",
            "hospitalization_history_others" => "nullable|string",
            "medication_history" => "nullable|string",
            "medication_history_others" => "nullable|string",
            "allergies" => "nullable|string",
            "allergies_others" => "nullable|string",
            "family_history" => "nullable|string",
            "family_history_others" => "nullable|string",
            "social_history" => "nullable|string",
            "social_history_others" => "nullable|string",
            "immunization_history" => "nullable|string",
            "immunization_history_others" => "nullable|string",
            "review_of_systems" => "nullable|string",
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
