<?php

namespace App\Http\Controllers;

use App\Traits\RadiologyProcedureTrait;
use App\Repositories\RadiologyProcedureRepositories;

class RadiologyProcedureController extends Controller
{
    use RadiologyProcedureTrait;

    public $radiologyProcedureRepo;
    public function __construct(RadiologyProcedureRepositories $radiologyProcedureRepo)
    {
        $this->radiologyProcedureRepo = $radiologyProcedureRepo;
    }
}
