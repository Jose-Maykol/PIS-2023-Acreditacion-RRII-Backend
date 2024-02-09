<?php

use App\Http\Controllers\Api\DateSemestersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IdentificationContextController;

Route::middleware("auth:sanctum")->prefix('ident-context')->group(function () {
    Route::middleware('semesterisopen')->group(function () {
        Route::post('', [IdentificationContextController::class, 'createIdentificationContext']);
        Route::put('', [IdentificationContextController::class, 'updateIdentificationContext']);
    });
    Route::get('', [IdentificationContextController::class, 'getIdentificationContext']);
    Route::get('/export', [IdentificationContextController::class, 'reportContext']);
});
