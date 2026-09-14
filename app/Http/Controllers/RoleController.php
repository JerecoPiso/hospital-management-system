<?php

namespace App\Http\Controllers;

use App\Traits\RoleTrait;
use App\Repositories\RoleRepositories;

class RoleController extends Controller
{
    use RoleTrait;

    public $roleRepo;
    public function __construct(RoleRepositories $roleRepo)
    {
        $this->roleRepo = $roleRepo;
    }
}
