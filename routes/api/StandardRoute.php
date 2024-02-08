<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\NarrativasController;
use App\Http\Controllers\Api\StandardController;

Route::middleware("auth:sanctum")->prefix('standards')->group(function () {


    //Route::put('pruebas/{standard_id}', [StandardController::class, 'pruebas']);

    //rutas estandar
    //Route::get('status', [StandardController::class, 'standardStatus']);
    Route::post('', [StandardController::class, 'createEstandar']);
    Route::post('various', [StandardController::class, 'createStandards']);
    //Route::get('', [StandardController::class, 'listStandard']);
    Route::get('headers', [StandardController::class, 'listStandardHeaders']);
    Route::get('{standard_id}/header', [StandardController::class, 'showStandardHeader']);
    Route::put('{standard_id}/header', [StandardController::class, 'updateStandardHeader']);

    Route::get('{standard_id?}/status', [StandardController::class, 'listStandardStatus']);
    Route::put('{standard_id}/status/{standard_status_id}', [StandardController::class, 'updateStatusStandard']);

    Route::get('{standard_id}/users', [StandardController::class, 'listUserAssigned'])->where('standard_id', '[0-9]+');

    Route::get('narratives/export', [NarrativasController::class, 'reportAllNarratives']);

    


    Route::get('partial', [StandardController::class, 'listPartialStandard']);
    Route::get('users', [StandardController::class, 'listStandardsAssignment'] );
    Route::put('{standard_id}/assignment', [StandardController::class, 'changeStandardAssignment'])->where('standard_id', '[0-9]+');
    Route::get('standard-values', [StandardController::class, 'listEstandarValores']);
    Route::get('{standard_id}', [StandardController::class, 'showStandard'])->where('standard_id', '[0-9]+');
    Route::put('{standard_id}/users', [StandardController::class, 'updateUserStandard'])->where('standard_id', '[0-9]+');
    Route::get('{standard_id}/type-evidence/{evidence_type_id}', [StandardController::class, 'getStandardEvidences'])->where('standard_id', '[0-9]+')->where('evidence_type_id', '[0-9]+');
    Route::get('{standard_id}/evidences', [StandardController::class, 'searchEvidence'])->where('standard_id', '[0-9]+');
    Route::put('{standard_id}',  [StandardController::class, 'updateEstandar'])->where('standard_id', '[0-9]+');
    //Route::delete('{standard_id}', [StandardController::class, 'deleteEstandar'])->where('standard_id', '[0-9]+');

    //ruta narrativas

    Route::prefix('{standard_id}/narratives')->group(function () {

        //Route::post('', [NarrativasController::class, 'create']);
        Route::get('', [NarrativasController::class, 'get'])->where('narrative_id', '[0-9]+');
        //Route::put('', [NarrativasController::class, 'update']);
        Route::put('', [StandardController::class, 'updateNarrative']);

        
        Route::delete('', [NarrativasController::class, 'delete'])->where('narrative_id', '[0-9]+');
        Route::post('block', [StandardController::class, 'blockNarrative'])->where('narrative_id', '[0-9]+');
        Route::post('unlock', [StandardController::class, 'unlockNarrative'])->where('narrative_id', '[0-9]+');
        Route::post('enable', [StandardController::class, 'enableNarrative'])->where('narrative_id', '[0-9]+');
        //Route::get('', [NarrativasController::class, 'listNarratives']);
        //Route::get('last/{narrative_id}', [NarrativasController::class, 'lastNarrative'])->where('narrative_id', '[0-9]+');

    })->where('standard_id', '[0-9]+');
});// /api/standards/{standard}/narratives/{narrative}