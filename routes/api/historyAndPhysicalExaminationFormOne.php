<?php

use App\Http\Controllers\HistoryAndPhysicalExaminationFormOneController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('history-and-physical-examination-form-one')
    ->controller(HistoryAndPhysicalExaminationFormOneController::class)
    ->group(function (): void {
        $module = 'history-and-physical-examination-form-one';
        Route::get('/', 'list')->middleware("permission:$module,view");          // list history and physical examination form one entries
        Route::post('/', 'store')->middleware("permission:$module,create");       // create entry
        Route::get('/{pid}', 'view')->middleware("permission:$module,view");          // view entry
        Route::put('/{pid}', 'update')->middleware("permission:$module,update");    // update entry
        Route::delete('/{pid}', 'delete')->middleware("permission:$module,delete");  // delete entry
    });
