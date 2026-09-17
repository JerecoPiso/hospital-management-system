<?php

use App\Http\Controllers\LabTestController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('lab-tests')
    ->controller(LabTestController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:lab-tests,view');
        Route::post('/', 'store')->middleware('permission:lab-tests,create');
        Route::get('/{pid}', 'view')->middleware('permission:lab-tests,view');
        Route::put('/{pid}', 'update')->middleware('permission:lab-tests,update');
        Route::delete('/{pid}', 'delete')->middleware('permission:lab-tests,delete');
    });
