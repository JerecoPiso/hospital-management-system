<?php

use App\Http\Controllers\PertinentSignsAndSymptomsListController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('pertinent-signs-and-symptoms-lists')
    ->controller(PertinentSignsAndSymptomsListController::class)
    ->group(function (): void {
        $module = 'pertinent-signs-and-symptoms-lists';
        Route::get('/', 'list')->middleware("permission:$module,view");           // list items
        Route::post('/', 'store')->middleware("permission:$module,create");         // create item
        Route::get('/{pid}', 'view')->middleware("permission:$module,view");      // view item
        Route::put('/{pid}', 'update')->middleware("permission:$module,update");    // update item
        Route::delete('/{pid}', 'delete')->middleware("permission:$module,delete"); // delete item
    });
