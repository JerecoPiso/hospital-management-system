<?php

namespace App\Http\Controllers;

use App\Traits\FloorTrait;
use App\Repositories\FloorRepositories;

class FloorController extends Controller
{
    use FloorTrait;

    public $floorRepo;
    public function __construct(FloorRepositories $floorRepo)
    {
        $this->floorRepo = $floorRepo;
    }
}
