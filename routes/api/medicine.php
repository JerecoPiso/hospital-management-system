<?php

use App\Http\Controllers\MedicineController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('medicine')
    ->controller(MedicineController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:medicine,view');          // list notes
        Route::post('/', 'store')->middleware('permission:medicine,create');       // create note
        Route::get('/{pid}', 'view')->middleware('permission:medicine,view');          // view note
        Route::put('/{pid}', 'update')->middleware('permission:medicine,update');    // update note
        Route::delete('/{pid}', 'delete')->middleware('permission:medicine,delete');  // delete note
    });
