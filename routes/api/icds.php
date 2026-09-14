<?php

use App\Http\Controllers\IcdController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('icds')
    ->controller(IcdController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:icds,view');           // list icds
        Route::post('/', 'store')->middleware('permission:icds,create');       // create icd
        Route::get('/{pid}', 'view')->middleware('permission:icds,view');      // view icd
        Route::put('/{pid}', 'update')->middleware('permission:icds,update');  // update icd
        Route::delete('/{pid}', 'delete')->middleware('permission:icds,delete'); // delete icd
    });
