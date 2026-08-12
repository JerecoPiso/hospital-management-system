<?php

namespace App\Traits;

use App\Http\Requests\NursesNotes\StoreRequest;
use Illuminate\Http\Request;

trait NursesNotesTrait
{
    public function list(Request $request)
    {
        try {
            $filters = [];
            if ($request->has('patient_case_pid') || filled($request->input('patient_case_pid'))) {
                $note = $this->patientCaseRepo->searchByPid($request->input('patient_case_pid'));
                if ($note) {
                    $filters['patient_case_id'] = $note->id;
                }
            }
            $notes = $this->nurseNotesRepo->list($filters);
            return api_response($notes, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated["nurse_id"] = auth()->id();
            $note = $this->nurseNotesRepo->store($validated);
            return api_response(["note" => $note], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function view($order_pid)
    {
        try {
            $note = $this->nurseNotesRepo->searchByPid($order_pid);
            if (!$note) {
                return api_response([], false, "Nurses Notes not found", 404);
            }
            return api_response($note, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function update($order_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $note = $this->nurseNotesRepo->searchByPid($order_pid);
            if (!$note) {
                return api_response([], false, "Nurses Notes not found", 404);
            }
            $note = $this->nurseNotesRepo->update($note->id, $validated);
            if (!$note) {
                return api_response([], false, "Nurses Notes not updated", 500);
            }
            return api_response(["note" => $note], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function delete($order_pid)
    {
        try {
            $note = $this->nurseNotesRepo->searchByPid($order_pid);
            if (!$note) {
                return api_response([], false, "Nurses Notes not found", 404);
            }
            $delete = $this->nurseNotesRepo->delete($note);
            if (!$delete) {
                return api_response([], false, "Nurses Notes not deleted", 500);
            }
            return api_response([], true, "Nurses Notes deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
