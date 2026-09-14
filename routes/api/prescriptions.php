<?php

use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PrescriptionItemController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('prescriptions')
    ->controller(PrescriptionController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:prescriptions,view');
        Route::post('/', 'store')->middleware('permission:prescriptions,create');
        Route::get('/{pid}', 'view')->middleware('permission:prescriptions,view');
        Route::put('/{pid}', 'update')->middleware('permission:prescriptions,update');
        Route::patch('/{pid}/status', 'updateStatus')->middleware('permission:prescriptions,update');
        Route::delete('/{pid}', 'delete')->middleware('permission:prescriptions,delete');
    });

Route::middleware(['auth:sanctum'])->prefix('prescription-items')
    ->controller(PrescriptionItemController::class)
    ->group(function (): void {
        Route::get('/{pid}', 'view')->middleware('permission:prescription-items,view');
        Route::put('/{pid}', 'update')->middleware('permission:prescription-items,update');
        Route::delete('/{pid}', 'delete')->middleware('permission:prescription-items,delete');
    });
