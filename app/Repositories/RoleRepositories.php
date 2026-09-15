<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\RoleAccess;

class RoleRepositories
{
    public function list($filter = [])
    {
        $query = Role::withCount('users')->whereNot('id', Role::SUPER_ADMIN)->orderBy('id', 'desc');

        return api_list($query, $filter, ['name', 'description']);
    }

    public function searchByPid($pid)
    {
        try {
            $role = Role::with('roleAccesses')->withCount('users')->where('pid', $pid)->first();

            if (!$role) {
                return null;
            }

            return $role;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function store($data)
    {
        try {
            $role = Role::create($data);
            return $role->load('roleAccesses')->loadCount('users');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($role_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $role = Role::findOrFail($role_id);
            $role->update($data);

            return $role->load('roleAccesses')->loadCount('users');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function delete($role)
    {
        try {
            if (!$role) {
                return;
            }

            $role->delete();

            return true;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    /**
     * @param  array<int, array{module: string, can_view: bool, can_create: bool, can_update: bool, can_delete: bool}>  $accesses
     */
    public function syncAccess(Role $role, array $accesses)
    {
        foreach ($accesses as $access) {
            RoleAccess::updateOrCreate(
                ['role_id' => $role->id, 'module' => $access['module']],
                [
                    'can_view' => (bool) ($access['can_view'] ?? false),
                    'can_create' => (bool) ($access['can_create'] ?? false),
                    'can_update' => (bool) ($access['can_update'] ?? false),
                    'can_delete' => (bool) ($access['can_delete'] ?? false),
                ]
            );
        }

        return $role->load('roleAccesses');
    }
}
