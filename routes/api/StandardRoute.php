<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EstandarController;
use App\Http\Controllers\Api\NarrativasController;

Route::middleware("auth:sanctum")->prefix('standards')->group(function () {
   
   
   
    //rutas estandar
    Route::post('', [EstandarController::class, 'createEstandar']);
    Route::get('', [EstandarController::class, 'listEstandar']);
    Route::get('standard-values', [EstandarController::class, 'listEstandarValores']);
    Route::get('{standard}', [EstandarController::class, 'showEstandar'])->where('standard', '[0-9]+');
    Route::get('{standard}/evidencias', [EstandarController::class, 'getStandardEvidences'])->where('standard', '[0-9]+');
    Route::put('{standard}',  [EstandarController::class, 'updateEstandar'])->where('standard', '[0-9]+');
    Route::delete('{standard}', [EstandarController::class, 'deleteEstandar'])->where('standard', '[0-9]+');

    //ruta narrativas

    Route::prefix('{standard}/narratives')->group(function(){
        
        Route::post('', [NarrativasController::class, 'create']);
        Route::get('{narrative}', [NarrativasController::class, 'show'])->where('narrative', '[0-9]+');
        Route::put('{narrative}', [NarrativasController::class, 'update'])->where('narrative', '[0-9]+');
        Route::delete('{narrative}', [NarrativasController::class, 'delete'])->where('narrative', '[0-9]+');
        Route::get('', [NarrativasController::class, 'listNarrativas']);
        Route::get('last/{narrative}', [NarrativasController::class, 'ultimaNarrativa'])->where('narrative', '[0-9]+');
        
    })->where('plan','[0-9]+');

});// /api/standards/{standard}/narratives/{narrative}