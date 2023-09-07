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

Route::prefix('{id}/user-us')->group(function(){
        
        Route::get('{user_id}', function ($id, $user_id){
            return 'Response '.$id.'otra '.$user_id ;
        });//->where(['year' => '\d{4}']);
    });

Route::middleware("auth:sanctum")->prefix('auth')->group(function () {
    //Rutas de Auth
    Route::get('logout', [LoginController::class, 'logout']);
});