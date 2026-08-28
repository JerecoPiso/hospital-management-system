<?php

namespace App\Http\Controllers;

use App\Traits\PertinentSignsAndSymptomsListTrait;
use App\Repositories\PertinentSignsAndSymptomsListRepositories;

class PertinentSignsAndSymptomsListController extends Controller
{
    use PertinentSignsAndSymptomsListTrait;

    public $pertinentListRepo;

    public function __construct(PertinentSignsAndSymptomsListRepositories $pertinentListRepo)
    {
        $this->pertinentListRepo = $pertinentListRepo;
    }
}
