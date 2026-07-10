<?php

namespace App\Http\Requests\Patient;

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
            "firstname" => "required|string",
            "lastname" => "required|string",
            "middlename" => "nullable|string",
            "suffix" => "nullable|string|max:10",
            "birthdate" => "required|date",
            "gender" => "nullable|in:Male,Female,Other",
            "civil_status" => "nullable|string",
            "contact_number" => "nullable|string",
            "email_address" => "nullable|email",
            "religion" => "nullable|string",
            "birthplace" => "nullable|string",
            "occupation" => "nullable|string",
            "spouse_name" => "nullable|string",
            "admission_datetime" => "required|date",
            "chief_complaint" => "required|string",
            "initial_diagnosis" => "nullable|string",
            "final_diagnosis" => "nullable|string",
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
