<?php

use App\Http\Controllers\LabTestCategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('lab-test-categories')
    ->controller(LabTestCategoryController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:lab-test-categories,view');
        Route::post('/', 'store')->middleware('permission:lab-test-categories,create');
        Route::get('/{pid}', 'view')->middleware('permission:lab-test-categories,view');
        Route::put('/{pid}', 'update')->middleware('permission:lab-test-categories,update');
        Route::delete('/{pid}', 'delete')->middleware('permission:lab-test-categories,delete');
    });
