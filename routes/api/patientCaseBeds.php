<?php

use App\Http\Controllers\PatientCaseBedController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('patient-case-beds')
    ->controller(PatientCaseBedController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:patient-case-beds,view');           // list bed assignments (filter by patient_case_pid)
        Route::post('/', 'store')->middleware('permission:patient-case-beds,create');       // assign a bed to a patient case
        Route::get('/{pid}', 'view')->middleware('permission:patient-case-beds,view');      // view a bed assignment
        Route::put('/{pid}', 'update')->middleware('permission:patient-case-beds,update');  // update a bed assignment
        Route::delete('/{pid}', 'delete')->middleware('permission:patient-case-beds,delete'); // remove a bed assignment
    });
