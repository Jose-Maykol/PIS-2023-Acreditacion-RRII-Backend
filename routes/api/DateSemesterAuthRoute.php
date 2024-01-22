<?php

use App\Http\Controllers\Api\DateSemestersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware("auth:sanctum")->group(function () {
    Route::get('info', [DateSemestersController::class, 'infoDateSemester']);
    Route::post('close', [DateSemestersController::class, 'closeDateSemester']);
});