<?php

use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('settings')
    ->controller(SettingController::class)
    ->group(function (): void {
        Route::get('/', 'list')->middleware('permission:settings,view');           // list settings
        Route::post('/', 'store')->middleware('permission:settings,create');       // create setting
        Route::get('/getByname/{name}', 'getByName')->middleware('permission:fee-charges,view');      // view setting by name
        Route::get('/{pid}', 'view')->middleware('permission:settings,view');      // view setting
        Route::put('/{pid}', 'update')->middleware('permission:settings,update');  // update setting
        Route::delete('/{pid}', 'delete')->middleware('permission:settings,delete'); // delete setting
    });
