<?php

use App\Http\Controllers\Api\EvidencesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EvidenciasController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\FoldersController;

Route::middleware("auth:sanctum")->prefix('evidences')->group(function () {

    //ruta evidencias
    Route::post('', [EvidencesController::class, 'createFileEvidence']);
    Route::get('files/{file_id}/view', [EvidencesController::class, 'viewFile'])->where('file_id', '[0-9]+');
    Route::get('{evidence_id}/view', [EvidencesController::class, 'viewEvidence'])->where('evidence_id', '[0-9]+');
    Route::get('files/{file_id}/download', [EvidencesController::class, 'downloadFile'])->where('file_id', '[0-9]+');
    Route::patch('files/{file_id}/rename', [EvidencesController::class, 'renameFile'])->where('file_id', '[0-9]+');
    Route::patch('files/{file_id}/move', [EvidencesController::class, 'moveFile'])->where('file_id', '[0-9]+');
    Route::delete('files/{file_id}', [EvidencesController::class, 'deleteFile'])->where('file_id', '[0-9]+');  
    Route::get('export', [EvidencesController::class, 'reportAllEvidences']);

/*
    Route::post('various', [EvidenciasController::class, 'createEvidence']);
    Route::get('{evidence_id}/download', [EvidenciasController::class, 'download'])->where('evidence_id', '[0-9]+');
    Route::get('{evidence_id}/view', [EvidenciasController::class, 'view'])->where('evidence_id', '[0-9]+');
    Route::patch('{evidence_id}/rename', [EvidenciasController::class, 'rename'])->where('evidence_id', '[0-9]+');
    Route::patch('{evidence_id}/move', [EvidenciasController::class, 'move'])->where('evidence_id', '[0-9]+');
    Route::get('{evidence_id}', [EvidenciasController::class, 'show'])->where('evidence_id', '[0-9]+');
    Route::put('{evidence_id}', [EvidenciasController::class, 'update'])->where('evidence_id', '[0-9]+');
    Route::delete('{evidence_id}', [EvidenciasController::class, 'delete'])->where('evidence_id', '[0-9]+');
*/
}); // /api/evidences/{id}

Route::middleware("auth:sanctum")->prefix('folders')->group(function () {

    Route::get('', [FolderController::class, 'listFolder']);
    Route::post('', [FolderController::class, 'createFolder']);
    Route::patch('{folder_id}/rename', [FolderController::class, 'renameFolder'])->where('folder_id', '[0-9]+');
    Route::patch('{folder_id}/move', [FolderController::class, 'moveFolder'])->where('folder_id', '[0-9]+');
    Route::delete('{folder_id}', [FolderController::class, 'deleteFolder'])->where('folder_id', '[0-9]+');

});
