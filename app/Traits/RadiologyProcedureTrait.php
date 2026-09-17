<?php

namespace App\Traits;

use App\Http\Requests\RadiologyProcedure\StoreRequest;
use Illuminate\Http\Request;

trait RadiologyProcedureTrait
{
    public function list(Request $request)
    {
        try {
            $procedures = $this->radiologyProcedureRepo->list($request->only(['modality_pid', 'search', 'per_page', 'page']));
            return api_list_response($procedures['items'], $procedures['meta']);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $procedure = $this->radiologyProcedureRepo->store($validated);
            return api_response(["procedure" => $procedure], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function view($procedure_pid)
    {
        try {
            $procedure = $this->radiologyProcedureRepo->searchByPid($procedure_pid);
            if (!$procedure) {
                return api_response([], false, "Radiology procedure not found", 404);
            }
            return api_response($procedure, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($procedure_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $procedure = $this->radiologyProcedureRepo->searchByPid($procedure_pid);
            if (!$procedure) {
                return api_response([], false, "Radiology procedure not found", 404);
            }
            $procedure = $this->radiologyProcedureRepo->update($procedure->id, $validated);
            if (!$procedure) {
                return api_response([], false, "Radiology procedure not updated", 500);
            }
            return api_response(["procedure" => $procedure], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($procedure_pid)
    {
        try {
            $procedure = $this->radiologyProcedureRepo->searchByPid($procedure_pid);
            if (!$procedure) {
                return api_response([], false, "Radiology procedure not found", 404);
            }
            $delete = $this->radiologyProcedureRepo->delete($procedure);
            if (!$delete) {
                return api_response([], false, "Radiology procedure not deleted", 500);
            }
            return api_response([], true, "Radiology procedure deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
