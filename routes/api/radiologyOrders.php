<?php

use App\Http\Controllers\RadiologyOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('radiology-orders')
    ->controller(RadiologyOrderController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:radiology-orders,view');
        Route::post('/', 'store')->middleware('permission:radiology-orders,create');
        Route::get('/{pid}', 'view')->middleware('permission:radiology-orders,view');
        Route::put('/{pid}/status', 'updateStatus')->middleware('permission:radiology-orders,update');
        Route::put('/{pid}/report', 'saveReport')->middleware('permission:radiology-orders,update');
        Route::delete('/{pid}', 'delete')->middleware('permission:radiology-orders,delete');
    });
