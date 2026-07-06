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
            "general_appearance" => "nullable|string",
            "general_appearance_others" => "nullable|string",
            "skin" => "nullable|string",
            "skin_others" => "nullable|string",
            "heent" => "nullable|string",
            "heent_others" => "nullable|string",
            "neck" => "nullable|string",
            "neck_others" => "nullable|string",
            "chest_lungs" => "nullable|string",
            "chest_lungs_others" => "nullable|string",
            "cardiovascular" => "nullable|string",
            "cardiovascular_others" => "nullable|string",
            "abdomen" => "nullable|string",
            "abdomen_others" => "nullable|string",
            "genitourinary" => "nullable|string",
            "genitourinary_others" => "nullable|string",
            "rectal" => "nullable|string",
            "rectal_others" => "nullable|string",
            "musculoskeletal" => "nullable|string",
            "musculoskeletal_others" => "nullable|string",
            "neurological" => "nullable|string",
            "neurological_others" => "nullable|string",
            "psychiatric_mental_status" => "nullable|string",
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
