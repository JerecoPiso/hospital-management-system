<?php

use App\Http\Controllers\PatientRegistrationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('patient-registration')
    ->controller(PatientRegistrationController::class)
    ->group(function (): void {
        Route::get('/', 'list');          // list registered patients
        Route::post('/', 'store');         // register patient together with its patient case
        Route::get('/{pid}', 'view');          // view patient
        Route::put('/{pid}', 'update');    // update patient and its latest case
        Route::delete('/{pid}', 'delete');  // delete patient and its cases
    });
