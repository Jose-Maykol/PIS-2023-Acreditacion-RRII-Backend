<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\AccionesMejorasController;
use App\Http\Controllers\Api\CausasRaicesController;
use App\Http\Controllers\Api\FuentesController;
use App\Http\Controllers\Api\ObservationsController;
use App\Http\Controllers\Api\ProblemsOpportunitiesController;
use App\Http\Controllers\Api\RecursosController;
use App\Http\Controllers\Api\MetasController;
use App\Http\Controllers\Api\ResponsablesController;


Route::middleware("auth:sanctum")->prefix('plans')->group(function () {// /api/2023/A/plans/{plan}/goals/{goal}/


    //rutas plan
    Route::middleware('semesterisopen')->group(function () {
        Route::post('', [PlanController::class, 'createPlan']);
        Route::delete('{plan_id}', [PlanController::class, 'deletePlan'])->where('plan_id', '[0-9]+');
        Route::put('{plan_id}',[PlanController::class,'updatePlan'])->where('plan_id', '[0-9]+');
        Route::post('assign', [PlanController::class, 'assignPlan']);
    });
    Route::get('', [PlanController::class, 'listPlan']);
    Route::get('{plan_id}', [PlanController::class, 'showPlan'])->where('plan_id', '[0-9]+');
    Route::get('{plan_id}/evidences', [PlanController::class, 'showPlanEvidence'])->where('plan_id', '[0-9]+');
   
    Route::get('users', [PlanController::class, 'listPlanUser']);//Considerar anhadirlo a User
    Route::get('{plan_id}/export', [PlanController::class, 'exportPlan'])->where('plan_id', '[0-9]+');
    Route::get('export', [PlanController::class, 'exportPlanResume']);


    //Route::put('{plan_id}', [PlanController::class, 'update'])->where('plan_id', '[0-9]+');

});