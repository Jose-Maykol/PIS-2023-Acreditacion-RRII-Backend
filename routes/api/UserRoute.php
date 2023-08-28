<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;


Route::middleware("auth:sanctum")->prefix('users')->group(function () {

    //Rutas de Gestion de usarios
    Route::get('profile', [UserController::class, 'userProfile']);
    Route::put('update', [UserController::class, 'updateRoleEstado']);
    Route::post('register', [UserController::class, 'register']);
    Route::get('user', [UserController::class, 'listUser']);
    Route::get('enabled_users', [UserController::class, 'listUserHabilitados']);

});