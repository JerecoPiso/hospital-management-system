<?php

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('roles')
    ->controller(RoleController::class)
    ->group(function (): void {
        Route::get('/modules', 'modules')->middleware('permission:roles,view'); // canonical module list for the permissions matrix
        Route::get('/', 'list')->middleware('permission:roles,view');           // list roles
        Route::post('/', 'store')->middleware('permission:roles,create');       // create role
        Route::get('/{pid}', 'view')->middleware('permission:roles,view');      // view role + its permissions matrix
        Route::put('/{pid}', 'update')->middleware('permission:roles,update');  // update role name/description
        Route::put('/{pid}/access', 'updateAccess')->middleware('permission:roles,update'); // sync permissions matrix
        Route::delete('/{pid}', 'delete')->middleware('permission:roles,delete'); // delete role
    });
