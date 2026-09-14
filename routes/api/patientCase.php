<?php

use App\Http\Controllers\PatientCaseController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('patient-cases')
    ->controller(PatientCaseController::class)
    ->group(function (): void {
        Route::post('/', 'store')->middleware('permission:patient-cases,create'); // add a new case for an existing patient
        Route::get('/{pid}', 'view')->middleware('permission:patient-cases,view'); // view a patient case by pid
    });
