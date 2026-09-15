<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;

class UserRepositories
{

    public function list($filter = [])
    {
        $query = User::with('role')->whereNot('id', User::SUPER_ADMIN)->orderBy('id', 'desc');
        return api_list($query, $filter, ['firstname', 'lastname', 'middlename', 'email', 'license_no']);
    }
    public function searchByPid($pid)
    {
        try {

            $user = User::with('role')->where('pid', $pid)->first();

            if (!$user) {
                return [];
            }

            return $user;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }
    public function create($data)
    {
        try {
            $data = $this->resolveRoleId($data);
            $user = User::create($data);

            return $user->load('role');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }
    public function update($user_id, $data)
    {
        try {

            if (!$data) {
                return null;
            }

            $data = $this->resolveRoleId($data);
            $note = User::findOrFail($user_id);
            $note->update($data);

            return $note->load('role');
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    private function resolveRoleId($data)
    {
        if (array_key_exists('role_pid', $data)) {
            $data['role_id'] = $data['role_pid'] ? Role::where('pid', $data['role_pid'])->firstOrFail()->id : null;
            unset($data['role_pid']);
        }

        return $data;
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
