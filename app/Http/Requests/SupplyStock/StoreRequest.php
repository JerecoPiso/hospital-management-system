<?php

namespace App\Http\Requests\SupplyStock;

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
            "supply_pid" => "required|string|exists:supplies,pid",
            "quantity" => "nullable|integer|min:0",
            "purchase_price" => "nullable|numeric",
            "reorder_level" => "nullable|integer|min:0",
            "unit_type" => "nullable|string",
            "units_per_package" => "nullable|integer|min:1",
            "expiration_date" => "nullable|date",
            "batch_number" => "nullable|string",
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
