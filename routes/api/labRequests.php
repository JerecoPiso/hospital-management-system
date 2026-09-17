<?php

use App\Http\Controllers\LabRequestController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('lab-requests')
    ->controller(LabRequestController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:lab-requests,view');
        Route::post('/', 'store')->middleware('permission:lab-requests,create');
        Route::get('/{pid}', 'view')->middleware('permission:lab-requests,view');
        Route::put('/{pid}/status', 'updateStatus')->middleware('permission:lab-requests,update');
        Route::put('/{pid}/results', 'saveResults')->middleware('permission:lab-requests,update');
        Route::delete('/{pid}', 'delete')->middleware('permission:lab-requests,delete');
    });
