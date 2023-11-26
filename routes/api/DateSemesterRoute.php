<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DateSemestersController;

Route::middleware("auth:sanctum")->prefix('date-semester')->group(function () {
    Route::post('', [DateSemestersController::class, 'createDateSemester']);
    Route::put('', [DateSemestersController::class, 'updateDateSemester']);
});
Route::prefix('date-semester')->group(function () {
    Route::get('', [DateSemestersController::class, 'listDateSemester']);
});