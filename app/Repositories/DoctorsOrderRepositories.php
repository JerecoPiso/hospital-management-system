<?php

namespace App\Repositories;

use App\Models\DoctorsOrder;

class DoctorsOrderRepositories
{

    public function list($filter = [])
    {
        $order = DoctorsOrder::with(['user']);
        $order = $order->get();
        return $order->toArray();
    }
    public function searchByPid($pid)
    {
        try {

            $order = DoctorsOrder::where('pid', $pid)->first();

            if (!$order) {
                return [];
            }

            return $order;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {

            $order = DoctorsOrder::create($data);
            return $order;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($order_id, $data)
    {
        try {

            if (!$data) {
                return null;
            }

            $order = DoctorsOrder::findOrFail($order_id);
            $order->update($data);

            return $order;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function delete($data)
    {
        try {
            if (!$data) {
                return;
            }

            $data->delete();

            return true;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }
}
