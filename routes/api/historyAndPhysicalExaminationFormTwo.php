<?php

use App\Http\Controllers\HistoryAndPhysicalExaminationFormTwoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('history-and-physical-examination-form-two')
    ->controller(HistoryAndPhysicalExaminationFormTwoController::class)
    ->group(function (): void {
        $module = 'history-and-physical-examination-form-two';
        Route::get('/', 'list')->middleware("permission:$module,view");          // list history and physical examination form two entries
        Route::post('/', 'store')->middleware("permission:$module,create");       // create entry
        Route::get('/{pid}', 'view')->middleware("permission:$module,view");          // view entry
        Route::put('/{pid}', 'update')->middleware("permission:$module,update");    // update entry
        Route::delete('/{pid}', 'delete')->middleware("permission:$module,delete");  // delete entry
    });
