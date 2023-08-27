<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\AccionesMejorasController;
use App\Http\Controllers\Api\CausasRaicesController;
use App\Http\Controllers\Api\FuentesController;
use App\Http\Controllers\Api\ObservacionesController;
use App\Http\Controllers\Api\ProblemasOportunidadesController;
use App\Http\Controllers\Api\RecursosController;
use App\Http\Controllers\Api\MetasController;
use App\Http\Controllers\Api\ResponsablesController;


Route::middleware("auth:sanctum")->prefix('plans')->group(function () {// /api/plans/{plan}/goals/{goal}
   
    //rutas plan
    Route::post('', [PlanController::class, 'createPlan']);
    Route::get('', [PlanController::class, 'listPlan']);
    Route::get('{plan}', [PlanController::class, 'showPlan'])->where('plan', '[0-9]+');
    Route::get('{plan}/evidences', [PlanController::class, 'showPlanEvidence'])->where('plan', '[0-9]+');
    Route::delete('{plan}', [PlanController::class, 'deletePlan'])->where('plan', '[0-9]+');
    Route::put('{plan}', [PlanController::class, 'update'])->where('plan', '[0-9]+');
    Route::get('user', [PlanController::class, 'listPlanUser']);//Considerar anhadirlo a User
    Route::post('assign', [PlanController::class, 'assignPlan']);
    Route::get('{plan}/export', [PlanController::class, 'exportPlan'])->where('plan', '[0-9]+');
    //Route::put('plan',[PlanController::class,'updatePlan']);

    Route::prefix('{plan}/goals')->group(function(){
        //rutas metas
        Route::post('', [MetasController::class, 'create']);
        Route::put('{goal}', [MetasController::class, 'update'])->where('goal', '[0-9]+');
        Route::delete('{goal}', [MetasController::class, 'delete'])->where('goal', '[0-9]+');

    })->where('plan','[0-9]+');
    
    Route::prefix('{plan}/improvement-actions')->group(function(){
        
        //rutas accionesmejoras
        Route::post('', [AccionesMejorasController::class, 'create']);
        Route::put('{improvement_action}', [AccionesMejorasController::class, 'update'])->where('improvement_action', '[0-9]+');
        Route::delete('{improvement_action}', [AccionesMejorasController::class, 'delete'])->where('improvement_action', '[0-9]+');

    })->where('plan','[0-9]+');
    
    Route::prefix('{plan}/sources')->group(function(){

        //rutas fuentes
        Route::post('', [FuentesController::class, 'create']);
        Route::put('{source}', [FuentesController::class, 'update'])->where('source', '[0-9]+');
        Route::delete('{source}', [FuentesController::class, 'delete'])->where('source', '[0-9]+');

    })->where('plan','[0-9]+');
    
    Route::prefix('{plan}/observations')->group(function(){

        //rutas observaciones
        Route::post('', [ObservacionesController::class, 'create']);
        Route::put('{observation}', [ObservacionesController::class, 'update'])->where('observation', '[0-9]+');
        Route::delete('{observation}', [ObservacionesController::class, 'delete'])->where('observation', '[0-9]+');

    })->where('plan','[0-9]+');

    Route::prefix('{plan}/problems-opportunities')->group(function(){

        //rutas problemas
        Route::post('', [ProblemasOportunidadesController::class, 'create']);
        Route::put('{problem_opportunitie}', [ProblemasOportunidadesController::class, 'update'])->where('problem_opportunitie', '[0-9]+');
        Route::delete('{problem_opportunitie}', [ProblemasOportunidadesController::class, 'delete'])->where('problem_opportunitie', '[0-9]+');

    })->where('plan','[0-9]+');

    Route::prefix('{plan}/resources')->group(function(){
        
        //rutas recursos
        Route::post('', [RecursosController::class, 'create']);
        Route::put('{resource}', [RecursosController::class, 'update'])->where('resource', '[0-9]+');
        Route::delete('{resource}', [RecursosController::class, 'delete'])->where('resource', '[0-9]+');
       
    })->where('plan','[0-9]+');
    
    
    Route::prefix('{plan}/root-causes')->group(function(){

        //rutas casuasraiz
        Route::post('', [CausasRaicesController::class, 'create']);
        Route::put('{root_cause}', [CausasRaicesController::class, 'update'])->where('root_cause', '[0-9]+');
        Route::delete('{root_cause}', [CausasRaicesController::class, 'delete'])->where('root_cause', '[0-9]+');
       
    })->where('plan','[0-9]+');
    
    Route::prefix('{plan}/responsibles')->group(function(){
        //ruta responsables
        Route::post('', [ResponsablesController::class, 'create']);
        Route::put('{responsible}', [ResponsablesController::class, 'update'])->where('responsible', '[0-9]+');
        Route::delete('{responsible}', [ResponsablesController::class, 'delete'])->where('responsible', '[0-9]+');
       
    })->where('plan','[0-9]+');
   

});