<?php

use App\Http\Controllers\NursesNoteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('nurses-notes')
    ->controller(NursesNoteController::class)
    ->group(function (): void {
        Route::get('/', 'list');          // list notes
        Route::post('/', 'store');         // create note
        Route::get('/{pid}', 'view');          // view note
        Route::put('/{pid}', 'update');    // update note
        Route::delete('/{pid}', 'delete');  // delete note
    });
