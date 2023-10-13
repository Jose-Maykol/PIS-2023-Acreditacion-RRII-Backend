<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;


Route::middleware("auth:sanctum")->prefix('users')->group(function () {

    //Rutas de Gestion de usarios
    Route::get('profile', [UserController::class, 'userProfile']);
    Route::put('', [UserController::class, 'update']);
    Route::put('update_role/{user_id}', [UserController::class, 'updateRole']);
    Route::put('update_status/{user_id}', [UserController::class, 'updateStatus']);
    Route::post('', [UserController::class, 'register']);
    Route::get('', [UserController::class, 'listUser']);
    Route::get('enabled_users', [UserController::class, 'listEnabledUsers']);

});