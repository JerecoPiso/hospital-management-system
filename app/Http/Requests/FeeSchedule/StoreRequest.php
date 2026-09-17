<?php

namespace App\Http\Requests\FeeSchedule;

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
            "fee_category_pid" => "required|string|exists:fee_categories,pid",
            "code" => "required|string",
            "name" => "required|string",
            "standard_fee" => "required|numeric",
            "is_active" => "nullable|boolean",
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
