<?php

namespace App\Http\Controllers;

use App\Traits\DietTrait;
use App\Repositories\DietRepositories;

class DietController extends Controller
{
    use DietTrait;

    public $dietRepo;

    public function __construct(DietRepositories $dietRepo)
    {
        $this->dietRepo = $dietRepo;
    }
}
