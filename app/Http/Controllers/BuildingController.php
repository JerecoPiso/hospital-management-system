<?php

namespace App\Http\Controllers;

use App\Traits\BuildingTrait;
use App\Repositories\BuildingRepositories;

class BuildingController extends Controller
{
    use BuildingTrait;

    public $buildingRepo;
    public function __construct(BuildingRepositories $buildingRepo)
    {
        $this->buildingRepo = $buildingRepo;
    }
}
