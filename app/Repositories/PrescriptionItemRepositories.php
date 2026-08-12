<?php

namespace App\Repositories;

use App\Models\PrescriptionItem;

class PrescriptionItemRepositories
{
    public function searchByPid($pid)
    {
        try {
            $item = PrescriptionItem::with(['medicine', 'prescription'])->where('pid', $pid)->first();

            if (!$item) {
                return [];
            }

            return $item;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($item_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $item = PrescriptionItem::findOrFail($item_id);
            $item->update($data);

            return $item->load(['medicine', 'prescription']);
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
