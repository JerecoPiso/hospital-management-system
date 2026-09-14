<?php

use App\Http\Controllers\DietController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('diets')
    ->controller(DietController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:diets,view');           // list diets
        Route::post('/', 'store')->middleware('permission:diets,create');       // create diet
        Route::get('/{pid}', 'view')->middleware('permission:diets,view');      // view diet
        Route::put('/{pid}', 'update')->middleware('permission:diets,update');  // update diet
        Route::delete('/{pid}', 'delete')->middleware('permission:diets,delete'); // delete diet
    });
