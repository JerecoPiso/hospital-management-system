<?php

use App\Http\Controllers\HistoryAndPhysicalExaminationFormTwoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('history-and-physical-examination-form-two')
    ->controller(HistoryAndPhysicalExaminationFormTwoController::class)
    ->group(function (): void {
        Route::get('/', 'list');          // list history and physical examination form two entries
        Route::post('/', 'store');         // create entry
        Route::get('/{pid}', 'view');          // view entry
        Route::put('/{pid}', 'update');    // update entry
        Route::delete('/{pid}', 'delete');  // delete entry
    });
