<?php

use App\Http\Controllers\RadiologyModalityController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('radiology-modalities')
    ->controller(RadiologyModalityController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:radiology-modalities,view');
        Route::post('/', 'store')->middleware('permission:radiology-modalities,create');
        Route::get('/{pid}', 'view')->middleware('permission:radiology-modalities,view');
        Route::put('/{pid}', 'update')->middleware('permission:radiology-modalities,update');
        Route::delete('/{pid}', 'delete')->middleware('permission:radiology-modalities,delete');
    });
