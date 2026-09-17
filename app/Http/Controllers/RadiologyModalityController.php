<?php

namespace App\Http\Controllers;

use App\Traits\RadiologyModalityTrait;
use App\Repositories\RadiologyModalityRepositories;

class RadiologyModalityController extends Controller
{
    use RadiologyModalityTrait;

    public $radiologyModalityRepo;
    public function __construct(RadiologyModalityRepositories $radiologyModalityRepo)
    {
        $this->radiologyModalityRepo = $radiologyModalityRepo;
    }
}
