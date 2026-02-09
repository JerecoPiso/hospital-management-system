<?php

namespace App\Traits;

use App\Http\Requests\DoctorsOrder\StoreRequest;

trait DoctorsOrderTrait
{
    public function list()
    {
        try {
            $orders = $this->doctorsOrderRepo->list([]);
            return api_response($orders, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated["user_id"] = $authId = auth()->id();
            $order = $this->doctorsOrderRepo->store($validated);
            return api_response(["order" => $order], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
