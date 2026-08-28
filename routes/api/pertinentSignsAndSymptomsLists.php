<?php

use App\Http\Controllers\PertinentSignsAndSymptomsListController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('pertinent-signs-and-symptoms-lists')
    ->controller(PertinentSignsAndSymptomsListController::class)
    ->group(function (): void {
        Route::get('/', 'list');           // list items
        Route::post('/', 'store');         // create item
        Route::get('/{pid}', 'view');      // view item
        Route::put('/{pid}', 'update');    // update item
        Route::delete('/{pid}', 'delete'); // delete item
    });
