<?php

namespace App\Traits;

use App\Http\Requests\Setting\StoreRequest;
use App\Models\Setting;
use Illuminate\Http\Request;

trait SettingTrait
{
    public function list(Request $request)
    {
        try {
            $settings = $this->settingRepo->list($request->only(['search', 'per_page', 'page']));
            return api_list_response($settings['items'], $settings['meta']);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $setting = $this->settingRepo->store($validated);
            return api_response(["setting" => $setting], true, "Success", 201);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function getByName($name)
    {
        try {
            $setting = $this->settingRepo->getValue($name, Setting::DEFAULTS['professional_fee']);
            if (!$setting) {
                return api_response([], false, "Setting not found", 404);
            }
            return api_response($setting, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function view($setting_pid)
    {
        try {
            $setting = $this->settingRepo->searchByPid($setting_pid);
            if (!$setting) {
                return api_response([], false, "Setting not found", 404);
            }
            return api_response($setting, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update($setting_pid, StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $setting = $this->settingRepo->searchByPid($setting_pid);
            if (!$setting) {
                return api_response([], false, "Setting not found", 404);
            }
            // Default settings are looked up by name in code, so their name cannot change.
            if (array_key_exists($setting->name, Setting::DEFAULTS)) {
                $validated['name'] = $setting->name;
            }
            $setting = $this->settingRepo->update($setting->id, $validated);
            if (!$setting) {
                return api_response([], false, "Setting not updated", 500);
            }
            return api_response(["setting" => $setting], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function delete($setting_pid)
    {
        try {
            $setting = $this->settingRepo->searchByPid($setting_pid);
            if (!$setting) {
                return api_response([], false, "Setting not found", 404);
            }
            if (array_key_exists($setting->name, Setting::DEFAULTS)) {
                return api_response([], false, "Default settings cannot be deleted", 422);
            }
            $delete = $this->settingRepo->delete($setting);
            if (!$delete) {
                return api_response([], false, "Setting not deleted", 500);
            }
            return api_response([], true, "Setting deleted successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
