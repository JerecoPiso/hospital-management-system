<?php

use App\Http\Controllers\PatientCaseDietController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('patient-case-diets')
    ->controller(PatientCaseDietController::class)
    ->group(function (): void {
        Route::get('/', 'list');                 // list patient case diets (filter by patient_case_pid)
        Route::post('/', 'store');               // assign a diet to a patient case
        Route::get('/{pid}', 'view');            // view a patient case diet
        Route::put('/{pid}', 'update');          // update a patient case diet
        Route::delete('/{pid}', 'delete');       // remove a patient case diet
        Route::post('/{pid}/serve', 'serve');    // record that the diet was served
    });
