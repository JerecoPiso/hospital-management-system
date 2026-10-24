<?php

namespace App\Http\Requests\HistoryAndPhysicalExaminationFormTwo;

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
            "patient_case_pid" => "required|string|exists:patient_cases,pid",
            "findings" => "nullable|array",
            "findings.*" => "string|distinct|exists:examination_options,pid",
            "general_appearance_others" => "nullable|string",
            "skin_others" => "nullable|string",
            "heent_others" => "nullable|string",
            "neck_others" => "nullable|string",
            "chest_lungs_others" => "nullable|string",
            "cardiovascular_others" => "nullable|string",
            "abdomen_others" => "nullable|string",
            "genitourinary_others" => "nullable|string",
            "rectal_others" => "nullable|string",
            "musculoskeletal_others" => "nullable|string",
            "neurological_others" => "nullable|string",
            "psychiatric_mental_status_others" => "nullable|string",
            "assessment_impression" => "nullable|string",
            "plan_recommendations" => "nullable|string",
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
