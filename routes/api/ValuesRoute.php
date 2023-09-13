<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
//use App\Http\Controllers\Api\EstandarController;
=======
>>>>>>> a69d6a307b0170b4ea24fd353858b09cc23127a1
use App\Http\Controllers\Api\StandardController;
use App\Http\Controllers\Api\SourcesValuesController;
use App\Http\Controllers\Api\ResponsablesValoresController;
use App\Http\Controllers\Api\EstadosValoresController;
use App\Http\Controllers\Api\PlanController;

Route::prefix('values')->group(function(){


    Route::get('responsibles', [ResponsablesValoresController::class, 'listResponsablesValores']);
    //fuentes Valores
    Route::get('sources', [SourcesValuesController::class, 'listSourcesValues']);
    //Estados valores
    Route::get('status', [EstadosValoresController::class, 'listEstadosValores']);
    //Estandares  valores
    Route::get('standards', [StandardController::class, 'listEstandarValores']);

    Route::get('per',[PlanController::class, 'permissions']);
    
    
    
    
});

//Responsables Valores
