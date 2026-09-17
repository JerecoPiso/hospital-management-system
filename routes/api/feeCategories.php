<?php

use App\Http\Controllers\FeeCategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('fee-categories')
    ->controller(FeeCategoryController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:fee-categories,view');
        Route::post('/', 'store')->middleware('permission:fee-categories,create');
        Route::get('/{pid}', 'view')->middleware('permission:fee-categories,view');
        Route::put('/{pid}', 'update')->middleware('permission:fee-categories,update');
        Route::delete('/{pid}', 'delete')->middleware('permission:fee-categories,delete');
    });
