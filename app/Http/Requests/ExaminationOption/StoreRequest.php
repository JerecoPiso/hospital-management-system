<?php

namespace App\Http\Requests\ExaminationOption;

use App\Models\ExaminationOption;
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
            "type" => ["required", Rule::in(ExaminationOption::TYPES)],
            "code" => [
                "required",
                "string",
                "max:255",
                Rule::unique('examination_options', 'code')
                    ->where('type', $this->input('type'))
                    ->ignore($this->route('pid'), 'pid'),
            ],
            "description" => "required|string|max:255",
            "active" => "nullable|boolean",
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
