<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EstandarController;
use App\Http\Controllers\Api\SourcesValuesController;
use App\Http\Controllers\Api\ResponsablesValoresController;
use App\Http\Controllers\Api\EstadosValoresController;

Route::prefix('values')->group(function(){


    Route::get('responsibles', [ResponsablesValoresController::class, 'listResponsablesValores']);
    //fuentes Valores
    Route::get('sources', [SourcesValuesController::class, 'listFuentesValores']);
    //Estados valores
    Route::get('status', [EstadosValoresController::class, 'listEstadosValores']);
    //Estandares  valores
    Route::get('standards', [EstandarController::class, 'listEstandarValores']);

    
    
    
    /*Route::prefix('{id}/user-us')->group(function(){
        
        Route::get('{user_id}', function ($id, $user_id, $year, $semester){
            return 'Response '.$id.'otra '.$user_id.' '.$year.' '.$semester ;
        });//->where(['year' => '\d{4}']);
    });*/
    
});

//Responsables Valores
