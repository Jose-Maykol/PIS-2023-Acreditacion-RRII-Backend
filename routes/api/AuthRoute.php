<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;



//Rutas de Auth

Route::prefix('auth')->group(function (){
    
    Route::post('login', [LoginController::class, 'login']);
    Route::get('login/{provider}', [LoginController::class, 'redirectToProvider']);
    Route::get('login/{provider}/callback', [LoginController::class, 'handleProviderCallback']);

});

Route::middleware("auth:sanctum")->prefix('auth')->group(function () {
    //Rutas de Auth
    Route::get('logout', [LoginController::class, 'logout']);
});