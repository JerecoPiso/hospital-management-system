<?php

use App\Http\Controllers\SupplyChargeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('supply-charges')
    ->controller(SupplyChargeController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:supply-charges,view');           // list charges (filter by patient_case_pid)
        Route::post('/', 'store')->middleware('permission:supply-charges,create');       // charge supplies to a patient (deducts stock via FEFO)
        Route::get('/{pid}', 'view')->middleware('permission:supply-charges,view');      // view a supply charge
        Route::put('/{pid}', 'update')->middleware('permission:supply-charges,update');  // update a supply charge (re-deducts stock for the new items)
        Route::delete('/{pid}', 'delete')->middleware('permission:supply-charges,delete'); // delete a supply charge record (restores deducted stock)
    });
