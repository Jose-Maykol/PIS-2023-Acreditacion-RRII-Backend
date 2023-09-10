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
    Route::get('{standard_id}', [EstandarController::class, 'showEstandar'])->where('standard_id', '[0-9]+');
    Route::get('{standard_id}/evidencias', [EstandarController::class, 'getStandardEvidences'])->where('standard_id', '[0-9]+');
    Route::put('{standard_id}',  [EstandarController::class, 'updateEstandar'])->where('standard_id', '[0-9]+');
    Route::delete('{standard_id}', [EstandarController::class, 'deleteEstandar'])->where('standard_id', '[0-9]+');

    //ruta narrativas

    Route::prefix('{standard_id}/narratives')->group(function(){
        
        Route::post('', [NarrativasController::class, 'create']);
        Route::get('{narrative_id}', [NarrativasController::class, 'show'])->where('narrative_id', '[0-9]+');
        Route::put('{narrative_id}', [NarrativasController::class, 'update'])->where('narrative_id', '[0-9]+');
        Route::delete('{narrative_id}', [NarrativasController::class, 'delete'])->where('narrative_id', '[0-9]+');
        Route::get('', [NarrativasController::class, 'listNarrativas']);
        Route::get('last/{narrative_id}', [NarrativasController::class, 'ultimaNarrativa'])->where('narrative_id', '[0-9]+');
        
    })->where('standard_id','[0-9]+');

});// /api/standards/{standard}/narratives/{narrative}