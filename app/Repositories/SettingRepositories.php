<?php

namespace App\Repositories;

use App\Models\Setting;

class SettingRepositories
{
    public function list($filter = [])
    {
        $query = Setting::orderBy('id', 'asc');

        return api_list($query, $filter, ['name', 'value', 'description']);
    }

    public function searchByPid($pid)
    {
        try {
            $setting = Setting::where('pid', $pid)->first();

            if (!$setting) {
                return [];
            }

            return $setting;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function getValue($name, $default = null)
    {
        $setting = Setting::where('name', $name)->first();

        return $setting ? $setting->value : (Setting::DEFAULTS[$name] ?? $default);
    }

    public function store($data)
    {
        try {
            $setting = Setting::create($data);
            return $setting;
        } catch (\Exception $e) {
            throw new \Exception("An error has occured! " . $e->getMessage());
        }
    }

    public function update($setting_id, $data)
    {
        try {
            if (!$data) {
                return null;
            }

            $setting = Setting::findOrFail($setting_id);
            $setting->update($data);

            return $setting;
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
