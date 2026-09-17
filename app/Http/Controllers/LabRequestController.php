<?php

namespace App\Http\Controllers;

use App\Traits\LabRequestTrait;
use App\Repositories\LabRequestRepositories;

class LabRequestController extends Controller
{
    use LabRequestTrait;

    public $labRequestRepo;
    public function __construct(LabRequestRepositories $labRequestRepo)
    {
        $this->labRequestRepo = $labRequestRepo;
    }
}
