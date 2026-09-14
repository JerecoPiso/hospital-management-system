<?php

use App\Http\Controllers\PatientTypeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('patient-types')
    ->controller(PatientTypeController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:patient-types,view');           // list patient types
        Route::post('/', 'store')->middleware('permission:patient-types,create');         // create patient type
        Route::get('/{pid}', 'view')->middleware('permission:patient-types,view');      // view patient type
        Route::put('/{pid}', 'update')->middleware('permission:patient-types,update');    // update patient type
        Route::delete('/{pid}', 'delete')->middleware('permission:patient-types,delete'); // delete patient type
    });
