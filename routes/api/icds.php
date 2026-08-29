<?php

use App\Http\Controllers\IcdController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('icds')
    ->controller(IcdController::class)
    ->group(function (): void {
        Route::get('/', 'list');           // list icds
        Route::post('/', 'store');         // create icd
        Route::get('/{pid}', 'view');      // view icd
        Route::put('/{pid}', 'update');    // update icd
        Route::delete('/{pid}', 'delete'); // delete icd
    });
