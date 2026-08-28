<?php

use App\Http\Controllers\DietController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('diets')
    ->controller(DietController::class)
    ->group(function (): void {
        Route::get('/', 'list');           // list diets
        Route::post('/', 'store');         // create diet
        Route::get('/{pid}', 'view');      // view diet
        Route::put('/{pid}', 'update');    // update diet
        Route::delete('/{pid}', 'delete'); // delete diet
    });
