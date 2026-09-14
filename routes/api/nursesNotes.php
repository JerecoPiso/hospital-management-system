<?php

use App\Http\Controllers\NursesNoteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('nurses-notes')
    ->controller(NursesNoteController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:nurses-notes,view');          // list notes
        Route::post('/', 'store')->middleware('permission:nurses-notes,create');       // create note
        Route::get('/{pid}', 'view')->middleware('permission:nurses-notes,view');          // view note
        Route::put('/{pid}', 'update')->middleware('permission:nurses-notes,update');    // update note
        Route::delete('/{pid}', 'delete')->middleware('permission:nurses-notes,delete');  // delete note
    });
