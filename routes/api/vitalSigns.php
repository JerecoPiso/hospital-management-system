<?php

use App\Http\Controllers\VitalSignController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('vital-signs')
    ->controller(VitalSignController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:vital-signs,view');           // list vital signs
        Route::post('/', 'store')->middleware('permission:vital-signs,create');         // create vital sign
        Route::get('/{pid}', 'view')->middleware('permission:vital-signs,view');      // view vital sign
        Route::put('/{pid}', 'update')->middleware('permission:vital-signs,update');    // update vital sign
        Route::delete('/{pid}', 'delete')->middleware('permission:vital-signs,delete'); // delete vital sign
        Route::get('/latest-vital-signs/{case_pid}', 'getLatestVitalSign')->middleware('permission:vital-signs,view');           // latest vital signs
    });
