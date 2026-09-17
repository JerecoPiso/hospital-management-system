<?php

namespace App\Http\Controllers;

use App\Traits\RadiologyOrderTrait;
use App\Repositories\RadiologyOrderRepositories;

class RadiologyOrderController extends Controller
{
    use RadiologyOrderTrait;

    public $radiologyOrderRepo;
    public function __construct(RadiologyOrderRepositories $radiologyOrderRepo)
    {
        $this->radiologyOrderRepo = $radiologyOrderRepo;
    }
}
