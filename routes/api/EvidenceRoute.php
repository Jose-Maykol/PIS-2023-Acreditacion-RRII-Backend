<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EvidenciasController;

Route::middleware("auth:sanctum")->prefix('evidences')->group(function () {
   
    //ruta evidencias
    Route::post('', [EvidenciasController::class, 'create']);
    Route::post('various', [EvidenciasController::class, 'createEvidence']);
    Route::get('{evidence_id}/download', [EvidenciasController::class, 'download'])->where('evidence_id', '[0-9]+');
    Route::get('{evidence_id}/view', [EvidenciasController::class, 'view'])->where('evidence_id', '[0-9]+');
    Route::get('{evidence_id}', [EvidenciasController::class, 'show'])->where('evidence_id', '[0-9]+');
    Route::put('{evidence_id}', [EvidenciasController::class, 'update'])->where('evidence_id', '[0-9]+');
    Route::delete('{evidence_id}', [EvidenciasController::class, 'delete'])->where('evidence_id', '[0-9]+');

});// /api/evidences/{id}

// /api/2023/A/evidences{id}