<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NarrativasController;
use App\Http\Controllers\Api\StandardController;

Route::middleware("auth:sanctum")->prefix('standards')->group(function () {
   
   
   //Route::put('pruebas/{standard_id}', [StandardController::class, 'pruebas']);

    //rutas estandar
    Route::post('', [StandardController::class, 'createEstandar']);
    Route::get('', [StandardController::class, 'listEstandar']);
    Route::get('standard-values', [StandardController::class, 'listEstandarValores']);
    Route::get('{standard_id}', [StandardController::class, 'showEstandar'])->where('standard_id', '[0-9]+');
    Route::put('{standard_id}/users', [StandardController::class, 'updateUserStandard'])->where('standard_id', '[0-9]+');
    Route::get('{standard_id}/type-evidence/{evidence_type_id}', [StandardController::class, 'getStandardEvidences'])->where('standard_id', '[0-9]+')->where('evidence_type_id', '[0-9]+');
    Route::get('{standard_id}/evidences', [StandardController::class, 'searchEvidence'])->where('standard_id', '[0-9]+')->where('evidence_type_id', '[0-9]+');
    Route::put('{standard_id}',  [StandardController::class, 'updateEstandar'])->where('standard_id', '[0-9]+');
    Route::delete('{standard_id}', [StandardController::class, 'deleteEstandar'])->where('standard_id', '[0-9]+');

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