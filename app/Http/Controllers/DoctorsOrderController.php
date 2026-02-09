<?php

namespace App\Http\Controllers;

use App\Traits\DoctorsOrderTrait;
use App\Repositories\DoctorsOrderRepositories;
class DoctorsOrderController extends Controller
{
    //
    use DoctorsOrderTrait;
    public $doctorsOrderRepo;
    public function __construct(DoctorsOrderRepositories $doctorsOrderRepo)
    {
        $this->doctorsOrderRepo = $doctorsOrderRepo;
    }
}
