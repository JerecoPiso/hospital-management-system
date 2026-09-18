<?php

use App\Http\Controllers\PatientCaseDischargeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('patient-case-discharges')
    ->controller(PatientCaseDischargeController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:patient-case-discharges,view');           // list discharges (filter by patient_case_pid)
        Route::post('/', 'store')->middleware('permission:patient-case-discharges,create');        // record a discharge for a patient case
        Route::get('/{pid}', 'view')->middleware('permission:patient-case-discharges,view');       // view a discharge record
        Route::put('/{pid}', 'update')->middleware('permission:patient-case-discharges,update');   // update a discharge record
        Route::delete('/{pid}', 'delete')->middleware('permission:patient-case-discharges,delete'); // remove a discharge record
    });
