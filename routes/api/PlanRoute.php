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
    Route::post('', [PlanController::class, 'createPlan']);
    //Route::get('pruebas', [PlanController::class, 'pruebas']);
    //
    Route::get('', [PlanController::class, 'listPlan']);
    Route::get('{plan_id}', [PlanController::class, 'showPlan'])->where('plan_id', '[0-9]+');
    Route::get('{plan_id}/evidences', [PlanController::class, 'showPlanEvidence'])->where('plan_id', '[0-9]+');
    Route::delete('{plan_id}', [PlanController::class, 'deletePlan'])->where('plan_id', '[0-9]+');
    Route::put('{plan_id}',[PlanController::class,'updatePlan'])->where('plan_id', '[0-9]+');
    
    Route::get('users', [PlanController::class, 'listPlanUser']);//Considerar anhadirlo a User
    Route::post('assign', [PlanController::class, 'assignPlan']);
    Route::get('{plan_id}/export', [PlanController::class, 'exportPlan'])->where('plan_id', '[0-9]+');

    //Route::put('{plan_id}', [PlanController::class, 'update'])->where('plan_id', '[0-9]+');

    Route::prefix('{plan_id}/goals')->group(function(){
        //rutas metas
        Route::post('', [MetasController::class, 'create']);
        Route::put('{goal_id}', [MetasController::class, 'update'])->where('goal_id', '[0-9]+');
        Route::delete('{goal_id}', [MetasController::class, 'delete'])->where('goal_id', '[0-9]+');

    })->where('plan_id','[0-9]+');
    
    Route::prefix('{plan_id}/improvement-actions')->group(function(){
        
        //rutas accionesmejoras
        Route::post('', [AccionesMejorasController::class, 'create']);
        Route::put('{improvement_action_id}', [AccionesMejorasController::class, 'update'])->where('improvement_action_id', '[0-9]+');
        Route::delete('{improvement_action_id}', [AccionesMejorasController::class, 'delete'])->where('improvement_action_id', '[0-9]+');

    })->where('plan_id','[0-9]+');
    
    Route::prefix('{plan_id}/sources')->group(function(){

        //rutas fuentes
        Route::post('', [FuentesController::class, 'create']);
        Route::put('{source_id}', [FuentesController::class, 'update'])->where('source_id', '[0-9]+');
        Route::delete('{source_id}', [FuentesController::class, 'delete'])->where('source_id', '[0-9]+');

    })->where('plan_id','[0-9]+');
    
    Route::prefix('{plan_id}/observations')->group(function(){

        //rutas observaciones
        Route::post('', [ObservationsController::class, 'create']);
        Route::put('{observation_id}', [ObservationsController::class, 'update'])->where('observation_id', '[0-9]+');
        Route::delete('{observation_id}', [ObservationsController::class, 'delete'])->where('observation_id', '[0-9]+');

    })->where('plan_id','[0-9]+');

    Route::prefix('{plan_id}/problems-opportunities')->group(function(){

        //rutas problemas
        Route::post('', [ProblemsOpportunitiesController::class, 'create']);
        Route::put('{problem_opportunitie_id}', [ProblemsOpportunitiesController::class, 'update'])->where('problem_opportunitie_id', '[0-9]+');
        Route::delete('{problem_opportunitie_id}', [ProblemsOpportunitiesController::class, 'delete'])->where('problem_opportunitie_id', '[0-9]+');

    })->where('plan_id','[0-9]+');

    Route::prefix('{plan_id}/resources')->group(function(){
        
        //rutas recursos
        Route::post('', [RecursosController::class, 'create']);
        Route::put('{resource_id}', [RecursosController::class, 'update'])->where('resource_id', '[0-9]+');
        Route::delete('{resource_id}', [RecursosController::class, 'delete'])->where('resource_id', '[0-9]+');
       
    })->where('plan_id','[0-9]+');
    
    
    Route::prefix('{plan_id}/root-causes')->group(function(){

        //rutas casuasraiz
        Route::post('', [CausasRaicesController::class, 'create']);
        Route::put('{root_cause_id}', [CausasRaicesController::class, 'update'])->where('root_cause_id', '[0-9]+');
        Route::delete('{root_cause_id}', [CausasRaicesController::class, 'delete'])->where('root_cause_id', '[0-9]+');
       
    })->where('plan_id','[0-9]+');
    
    Route::prefix('{plan_id}/responsibles')->group(function(){
        //ruta responsables
        Route::post('', [ResponsablesController::class, 'create']);
        Route::put('{responsible_id}', [ResponsablesController::class, 'update'])->where('responsible_id', '[0-9]+');
        Route::delete('{responsible_id}', [ResponsablesController::class, 'delete'])->where('responsible_id', '[0-9]+');
       
    })->where('plan_id','[0-9]+');
   

});