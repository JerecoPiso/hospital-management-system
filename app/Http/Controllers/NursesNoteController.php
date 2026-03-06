<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\NursesNotesTrait;
use App\Repositories\NursesNotesRepositories;

class NursesNoteController extends Controller
{
    //
    use NursesNotesTrait;

    public $nurseNotesRepo;
    public function __construct(NursesNotesRepositories $nurseNotesRepo)
    {
        $this->nurseNotesRepo = $nurseNotesRepo;
    }
}
