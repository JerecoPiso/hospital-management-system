<?php

namespace App\Http\Controllers;

use App\Traits\WardTrait;
use App\Repositories\WardRepositories;

class WardController extends Controller
{
    use WardTrait;

    public $wardRepo;
    public function __construct(WardRepositories $wardRepo)
    {
        $this->wardRepo = $wardRepo;
    }
}
