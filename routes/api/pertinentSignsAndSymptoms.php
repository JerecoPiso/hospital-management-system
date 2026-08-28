<?php

use App\Http\Controllers\PertinentSignsAndSymptomsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('pertinent-signs-and-symptoms')
    ->controller(PertinentSignsAndSymptomsController::class)
    ->group(function (): void {
        Route::get('/', 'list');           // list entries (filter by patient_case_pid)
        Route::post('/', 'store');         // create entry
        Route::get('/{pid}', 'view');      // view entry
        Route::put('/{pid}', 'update');    // update entry
        Route::delete('/{pid}', 'delete'); // delete entry
    });
