<?php

use App\Http\Controllers\PertinentSignsAndSymptomsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('pertinent-signs-and-symptoms')
    ->controller(PertinentSignsAndSymptomsController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:pertinent-signs-and-symptoms,view');           // list entries (filter by patient_case_pid)
        Route::post('/', 'store')->middleware('permission:pertinent-signs-and-symptoms,create');         // create entry
        Route::get('/{pid}', 'view')->middleware('permission:pertinent-signs-and-symptoms,view');      // view entry
        Route::put('/{pid}', 'update')->middleware('permission:pertinent-signs-and-symptoms,update');    // update entry
        Route::delete('/{pid}', 'delete')->middleware('permission:pertinent-signs-and-symptoms,delete'); // delete entry
    });
