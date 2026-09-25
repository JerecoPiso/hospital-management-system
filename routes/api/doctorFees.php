<?php

use App\Http\Controllers\DoctorFeeController;
use Illuminate\Support\Facades\Route;

// Professional fees are charged from the Fee Charges page, so they share its permission module.
Route::middleware(['auth:sanctum'])->prefix('doctor-fees')
    ->controller(DoctorFeeController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:fee-charges,view');
        Route::get('/doctors', 'doctors')->middleware('permission:fee-charges,view');
        Route::post('/', 'store')->middleware('permission:fee-charges,create');
        Route::get('/{pid}', 'view')->middleware('permission:fee-charges,view');
        Route::put('/{pid}', 'update')->middleware('permission:fee-charges,update');
        Route::delete('/{pid}', 'delete')->middleware('permission:fee-charges,delete');
    });
