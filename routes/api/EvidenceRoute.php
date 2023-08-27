<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EvidenciasController;

Route::middleware("auth:sanctum")->prefix('evidences')->group(function () {
   
    //ruta evidencias
    Route::post('', [EvidenciasController::class, 'create']);
    Route::post('various', [EvidenciasController::class, 'createEvidence']);
    Route::get('{evidence}/download', [EvidenciasController::class, 'download'])->where('evidence', '[0-9]+');
    Route::get('{evidence}/view', [EvidenciasController::class, 'view'])->where('evidence', '[0-9]+');
    Route::get('{evidence}', [EvidenciasController::class, 'show'])->where('evidence', '[0-9]+');
    Route::put('{evidence}', [EvidenciasController::class, 'update'])->where('evidence', '[0-9]+');
    Route::delete('{evidence}', [EvidenciasController::class, 'delete'])->where('evidence', '[0-9]+');

});