<?php

namespace App\Http\Controllers;

use App\Traits\SettingTrait;
use App\Repositories\SettingRepositories;

class SettingController extends Controller
{
    use SettingTrait;

    public $settingRepo;

    public function __construct(SettingRepositories $settingRepo)
    {
        $this->settingRepo = $settingRepo;
    }
}
