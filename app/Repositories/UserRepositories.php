<?php

namespace App\Repositories;

use App\Models\User;

class UserRepositories
{

    public function list($filter = [])
    {
        $query = User::query()->orderBy('id', 'desc');

        return api_list($query, $filter, ['firstname', 'lastname', 'middlename', 'email', 'license_no']);
    }
    public function searchByPid($pid)
    {
        try {

            $user = User::where('pid', $pid)->first();

            if (!$user) {
                return [];
            }

            return $user;
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

            $note = User::findOrFail($user_id);
            $note->update($data);

            return $note;
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
