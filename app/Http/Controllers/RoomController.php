<?php

namespace App\Http\Controllers;

use App\Traits\RoomTrait;
use App\Repositories\RoomRepositories;

class RoomController extends Controller
{
    use RoomTrait;

    public $roomRepo;
    public function __construct(RoomRepositories $roomRepo)
    {
        $this->roomRepo = $roomRepo;
    }
}
