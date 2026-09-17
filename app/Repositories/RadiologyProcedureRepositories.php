<?php

namespace App\Repositories;

use App\Models\RadiologyModality;
use App\Models\RadiologyProcedure;

class RadiologyProcedureRepositories
{
    public function list($filter = [])
    {
        $query = RadiologyProcedure::with(['modality'])->orderBy('id', 'desc');

        if (!empty($filter['modality_pid'])) {
            $query->whereHas('modality', function ($q) use ($filter) {
                $q->where('pid', $filter['modality_pid']);
            });
        }

        return api_list($query, $filter, ['code', 'name', 'body_part', 'modality.name', 'modality.code']);
    }

    public function searchByPid($pid)
    {
        try {
            $procedure = RadiologyProcedure::with(['modality'])->where('pid', $pid)->first();

            if (!$procedure) {
                return [];
            }

            return $procedure;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $data = $this->resolveModalityId($data);
            $procedure = RadiologyProcedure::create($data);
            return $procedure->load('modality');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($procedure_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $data = $this->resolveModalityId($data);
            $procedure = RadiologyProcedure::findOrFail($procedure_id);
            $procedure->update($data);

            return $procedure->load('modality');
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

    private function resolveModalityId($data)
    {
        if (!empty($data['modality_pid'])) {
            $data['modality_id'] = RadiologyModality::where('pid', $data['modality_pid'])->firstOrFail()->id;
            unset($data['modality_pid']);
        }

        return $data;
    }
}
