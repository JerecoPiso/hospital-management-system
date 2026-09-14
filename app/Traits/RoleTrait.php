<?php

namespace App\Traits;

use App\Http\Requests\Role\StoreRequest;
use App\Http\Requests\Role\SyncAccessRequest;
use Illuminate\Http\Request;

trait RoleTrait
{
    public function list(Request $request)
    {
        try {
            $roles = $this->roleRepo->list($request->only(['search', 'per_page', 'page']));
            return api_list_response($roles['items'], $roles['meta']);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function modules()
    {
        return api_response(require config_path('modules.php'), true, "Success", 200);
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();

            $role = $this->roleRepo->store($validated);
            return api_response(["role" => $role], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function view($role_pid)
    {
        try {
            $role = $this->roleRepo->searchByPid($role_pid);
            if (!$role) {
                return api_response([], false, "Role not found", 404);
            }

            $modules = collect(require config_path('modules.php'));
            $existing = $role->roleAccesses->keyBy('module');
            $accesses = $modules->map(function ($module) use ($existing) {
                $access = $existing->get($module['key']);
                return [
                    'module' => $module['key'],
                    'label' => $module['label'],
                    'can_view' => (bool) ($access->can_view ?? false),
                    'can_create' => (bool) ($access->can_create ?? false),
                    'can_update' => (bool) ($access->can_update ?? false),
                    'can_delete' => (bool) ($access->can_delete ?? false),
                ];
            })->values();

            $data = $role->toArray();
            $data['accesses'] = $accesses;

            return api_response($data, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($role_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $role = $this->roleRepo->searchByPid($role_pid);
            if (!$role) {
                return api_response([], false, "Role not found", 404);
            }
            $role = $this->roleRepo->update($role->id, $validated);
            if (!$role) {
                return api_response([], false, "Role not updated", 500);
            }
            return api_response(["role" => $role], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function updateAccess($role_pid, SyncAccessRequest $request)
    {
        try {
            $role = $this->roleRepo->searchByPid($role_pid);
            if (!$role) {
                return api_response([], false, "Role not found", 404);
            }
            $role = $this->roleRepo->syncAccess($role, $request->validated()['accesses']);
            return api_response(["role" => $role], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($role_pid)
    {
        try {
            $role = $this->roleRepo->searchByPid($role_pid);
            if (!$role) {
                return api_response([], false, "Role not found", 404);
            }
            if ($role->users()->exists()) {
                return api_response([], false, "Cannot delete a role that is still assigned to users.", 422);
            }
            $deleted = $this->roleRepo->delete($role);
            if (!$deleted) {
                return api_response([], false, "Role not deleted", 500);
            }
            return api_response([], true, "Role deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
