<?php

use App\Http\Controllers\PatientCaseDietController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('patient-case-diets')
    ->controller(PatientCaseDietController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:patient-case-diets,view');                 // list patient case diets (filter by patient_case_pid)
        Route::post('/', 'store')->middleware('permission:patient-case-diets,create');               // assign a diet to a patient case
        Route::get('/{pid}', 'view')->middleware('permission:patient-case-diets,view');            // view a patient case diet
        Route::put('/{pid}', 'update')->middleware('permission:patient-case-diets,update');          // update a patient case diet
        Route::delete('/{pid}', 'delete')->middleware('permission:patient-case-diets,delete');       // remove a patient case diet
        Route::post('/{pid}/serve', 'serve')->middleware('permission:patient-case-diets,update');    // record that the diet was served
    });
