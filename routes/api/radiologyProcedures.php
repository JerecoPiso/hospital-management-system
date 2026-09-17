<?php

use App\Http\Controllers\RadiologyProcedureController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('radiology-procedures')
    ->controller(RadiologyProcedureController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:radiology-procedures,view');
        Route::post('/', 'store')->middleware('permission:radiology-procedures,create');
        Route::get('/{pid}', 'view')->middleware('permission:radiology-procedures,view');
        Route::put('/{pid}', 'update')->middleware('permission:radiology-procedures,update');
        Route::delete('/{pid}', 'delete')->middleware('permission:radiology-procedures,delete');
    });
