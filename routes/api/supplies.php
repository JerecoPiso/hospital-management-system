<?php

use App\Http\Controllers\SupplyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('supplies')
    ->controller(SupplyController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:supplies,view');           // list supplies
        Route::post('/', 'store')->middleware('permission:supplies,create');         // create supply
        Route::get('/{pid}', 'view')->middleware('permission:supplies,view');      // view supply
        Route::put('/{pid}', 'update')->middleware('permission:supplies,update');    // update supply
        Route::delete('/{pid}', 'delete')->middleware('permission:supplies,delete'); // delete supply
    });
