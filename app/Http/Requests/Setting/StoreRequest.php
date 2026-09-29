<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => [
                "required",
                "string",
                "max:255",
                "regex:/^[a-z0-9_]+$/",
                Rule::unique('settings', 'name')->ignore($this->route('pid'), 'pid'),
            ],
            "value" => "required|string|max:255",
            "description" => "nullable|string",
        ];
    }

    public function messages(): array
    {
        return [
            "name.regex" => "The name may only contain lowercase letters, numbers and underscores.",
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
