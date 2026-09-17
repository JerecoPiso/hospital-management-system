<?php

namespace App\Http\Controllers;

use App\Traits\LabTestParameterTrait;
use App\Repositories\LabTestParameterRepositories;

class LabTestParameterController extends Controller
{
    use LabTestParameterTrait;

    public $labTestParameterRepo;
    public function __construct(LabTestParameterRepositories $labTestParameterRepo)
    {
        $this->labTestParameterRepo = $labTestParameterRepo;
    }
}
