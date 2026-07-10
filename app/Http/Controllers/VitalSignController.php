<?php

namespace App\Http\Controllers;

use App\Traits\VitalSignTrait;
use App\Repositories\VitalSignRepositories;
class VitalSignController extends Controller
{
    //
    use VitalSignTrait;
    public $vitalSignRepo;
    public function __construct(VitalSignRepositories $vitalSignRepo)
    {
        $this->vitalSignRepo = $vitalSignRepo;
    }
}
