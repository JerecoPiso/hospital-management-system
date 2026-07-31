<?php

namespace App\Http\Requests\SupplyDistribution;

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
            "supply_stock_pid" => "required|string|exists:supply_stocks,pid",
            "station_pid" => "required|string|exists:stations,pid",
            "quantity" => "required|integer|min:1",
            "distributed_at" => "nullable|date",
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
