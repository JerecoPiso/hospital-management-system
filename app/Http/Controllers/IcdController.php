<?php

namespace App\Http\Controllers;

use App\Traits\IcdTrait;
use App\Repositories\IcdRepositories;

class IcdController extends Controller
{
    use IcdTrait;

    public $icdRepo;

    public function __construct(IcdRepositories $icdRepo)
    {
        $this->icdRepo = $icdRepo;
    }
}
