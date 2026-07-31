<?php

namespace App\Http\Controllers;

use App\Traits\StationTrait;
use App\Repositories\StationRepositories;

class StationController extends Controller
{
    use StationTrait;

    public $stationRepo;
    public function __construct(StationRepositories $stationRepo)
    {
        $this->stationRepo = $stationRepo;
    }
}
