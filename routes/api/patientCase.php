<?php

use App\Http\Controllers\PatientCaseController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('patient-cases')
    ->controller(PatientCaseController::class)
    ->group(function (): void {
        Route::post('/', 'store'); // add a new case for an existing patient
    });
