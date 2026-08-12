<?php

use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PrescriptionItemController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('prescriptions')
    ->controller(PrescriptionController::class)
    ->group(function (): void {
        Route::get('/', 'list');
        Route::post('/', 'store');
        Route::get('/{pid}', 'view');
        Route::put('/{pid}', 'update');
        Route::delete('/{pid}', 'delete');
    });

Route::middleware(['auth:sanctum'])->prefix('prescription-items')
    ->controller(PrescriptionItemController::class)
    ->group(function (): void {
        Route::get('/{pid}', 'view');
        Route::put('/{pid}', 'update');
        Route::delete('/{pid}', 'delete');
    });
