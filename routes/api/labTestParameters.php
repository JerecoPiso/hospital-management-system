<?php

use App\Http\Controllers\LabTestParameterController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('lab-test-parameters')
    ->controller(LabTestParameterController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:lab-test-parameters,view');
        Route::post('/', 'store')->middleware('permission:lab-test-parameters,create');
        Route::get('/{pid}', 'view')->middleware('permission:lab-test-parameters,view');
        Route::put('/{pid}', 'update')->middleware('permission:lab-test-parameters,update');
        Route::delete('/{pid}', 'delete')->middleware('permission:lab-test-parameters,delete');
    });
