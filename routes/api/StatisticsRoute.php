<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StatisticsController;

Route::middleware("auth:sanctum")->prefix('statistics')->group(function () {
   
    Route::get('plans', [StatisticsController::class, 'planStatistics']);

});