<?php

use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('patient')
    ->controller(PatientController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:patient,view');          // list registered patients
        Route::post('/', 'store')->middleware('permission:patient,create');       // register patient together with its patient case
        Route::get('/{pid}', 'view')->middleware('permission:patient,view');          // view patient
        Route::put('/{pid}', 'update')->middleware('permission:patient,update');    // update patient and its latest case
        Route::delete('/{pid}', 'delete')->middleware('permission:patient,delete');  // delete patient and its cases
    });
