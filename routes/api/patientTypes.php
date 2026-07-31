<?php

use App\Http\Controllers\PatientTypeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('patient-types')
    ->controller(PatientTypeController::class)
    ->group(function (): void {
        Route::get('/', 'list');           // list patient types
        Route::post('/', 'store');         // create patient type
        Route::get('/{pid}', 'view');      // view patient type
        Route::put('/{pid}', 'update');    // update patient type
        Route::delete('/{pid}', 'delete'); // delete patient type
    });
