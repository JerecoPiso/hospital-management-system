<?php

namespace App\Http\Controllers;

use App\Traits\BedTrait;
use App\Repositories\BedRepositories;

class BedController extends Controller
{
    use BedTrait;

    public $bedRepo;
    public function __construct(BedRepositories $bedRepo)
    {
        $this->bedRepo = $bedRepo;
    }
}
