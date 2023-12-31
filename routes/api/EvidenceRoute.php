<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EvidenciasController;
use App\Http\Controllers\Api\FoldersController;

Route::middleware("auth:sanctum")->prefix('evidences')->group(function () {
   
    //ruta evidencias
    Route::post('', [EvidenciasController::class, 'create']);

    Route::get('export', [EvidenciasController::class, 'reportAllEvidences']);


    Route::post('various', [EvidenciasController::class, 'createEvidence']);
    Route::get('{evidence_id}/download', [EvidenciasController::class, 'download'])->where('evidence_id', '[0-9]+');
    Route::get('{evidence_id}/view', [EvidenciasController::class, 'view'])->where('evidence_id', '[0-9]+');
    Route::patch('{evidence_id}/rename', [EvidenciasController::class, 'rename'])->where('evidence_id', '[0-9]+');
    Route::patch('{evidence_id}/move', [EvidenciasController::class, 'move'])->where('evidence_id', '[0-9]+');
    Route::get('{evidence_id}', [EvidenciasController::class, 'show'])->where('evidence_id', '[0-9]+');
    Route::put('{evidence_id}', [EvidenciasController::class, 'update'])->where('evidence_id', '[0-9]+');
    Route::delete('{evidence_id}', [EvidenciasController::class, 'delete'])->where('evidence_id', '[0-9]+');
    Route::get('folder', [FoldersController::class, 'list']);
    Route::post('folder', [FoldersController::class, 'create']);
    Route::patch('folder/{folder_id}/rename', [FoldersController::class, 'rename'])->where('folder_id', '[0-9]+');
    Route::delete('folder/{folder_id}', [FoldersController::class, 'delete'])->where('folder_id', '[0-9]+');
    Route::patch('folder/{folder_id}/move', [FoldersController::class, 'move'])->where('folder_id', '[0-9]+');
});// /api/evidences/{id}

// /api/2023/A/evidences{id}