<?php
/*
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EstandarController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\AccionesMejorasController;
use App\Http\Controllers\Api\CausasRaicesController;
use App\Http\Controllers\Api\FuentesController;
use App\Http\Controllers\Api\ObservacionesController;
use App\Http\Controllers\Api\ProblemasOportunidadesController;
use App\Http\Controllers\Api\RecursosController;
use App\Http\Controllers\Api\MetasController;
use App\Http\Controllers\Api\FuentesValoresController;
use App\Http\Controllers\Api\ResponsablesValoresController;
use App\Http\Controllers\Api\ResponsablesController;
use App\Http\Controllers\Api\EstadosValoresController;
use App\Http\Controllers\Api\EvidenciasController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\NarrativasController;
use App\Http\Controllers\Api\ActaController;
use App\Http\Controllers\Api\FoldersController;

//Rutas de Auth
Route::post('login', [LoginController::class, 'login'])->name('login');
Route::get('login/{provider}', [LoginController::class, 'redirectToProvider']);
Route::get('login/{provider}/callback', [LoginController::class, 'handleProviderCallback']);


//Responsables Valores
Route::get('responsables', [ResponsablesValoresController::class, 'listResponsablesValores']);
//fuentes Valores
Route::get('fuentes', [FuentesValoresController::class, 'listSourcesValues']);
//Estados valores
Route::get('estados', [EstadosValoresController::class, 'listEstadosValores']);
//Estandares  valores
Route::get('estandares', [EstandarController::class, 'listEstandarValores']);


Route::middleware("auth:sanctum")->group(function () {
    //Rutas de Auth
    Route::get('logout', [LoginController::class, 'logout']);

    //Rutas de Gestion de usarios
    Route::get('user-profile', [UserController::class, 'userProfile']);
    Route::put('update', [UserController::class, 'updateRoleEstado']);
    Route::post('register', [UserController::class, 'register']);
    Route::get('user', [UserController::class, 'listUser']);
    Route::get('enabled_users', [UserController::class, 'listUserHabilitados']);


    //rutas estandar
    Route::post('estandar', [EstandarController::class, 'createEstandar']);
    Route::get('estandar', [EstandarController::class, 'listEstandar']);
    Route::get('estandar-valores', [EstandarController::class, 'listEstandarValores']);
    Route::get('estandar/{id}', [EstandarController::class, 'showEstandar'])->where('id', '[0-9]+');
    Route::get('estandar/{id}/evidencias', [EstandarController::class, 'getStandardEvidences'])->where('id', '[0-9]+');
    Route::put('estandar/{id}',  [EstandarController::class, 'updateEstandar'])->where('id', '[0-9]+');
    Route::delete('estandar/{id}', [EstandarController::class, 'deleteEstandar'])->where('id', '[0-9]+');

    //rutas plan
    Route::post('plan', [PlanController::class, 'createPlan']);
    Route::get('plan', [PlanController::class, 'listPlan']);
    Route::get('plan/{id}', [PlanController::class, 'showPlan'])->where('id', '[0-9]+');
    Route::get('plan/{id}/evidencia', [PlanController::class, 'showPlanEvidence'])->where('id', '[0-9]+');
    Route::delete('plan/{id}', [PlanController::class, 'deletePlan'])->where('id', '[0-9]+');
    Route::put('plan/{id}', [PlanController::class, 'update'])->where('id', '[0-9]+');
    Route::get('plans/user', [PlanController::class, 'listPlanUser']);
    Route::post('plan/asignar', [PlanController::class, 'assignPlan']);
    Route::get('plan/export/{id}', [PlanController::class, 'exportPlan'])->where('id', '[0-9]+');
    //Route::put('plan',[PlanController::class,'updatePlan']);

    //rutas metas
    Route::post('meta', [MetasController::class, 'create']);
    Route::put('meta', [MetasController::class, 'update']);
    Route::delete('meta/{id}', [MetasController::class, 'delete'])->where('id', '[0-9]+');

    //rutas accionesmejoras
    Route::post('accionesmejora', [AccionesMejorasController::class, 'create']);
    Route::put('accionesmejora/{action}', [AccionesMejorasController::class, 'update']);
    Route::delete('accionesmejora/{action}', [AccionesMejorasController::class, 'delete'])->where('id', '[0-9]+');

    //rutas fuentes
    Route::post('sources', [FuentesController::class, 'create']);
    Route::put('sources/{source}', [FuentesController::class, 'update']);
    Route::delete('sources/{source}', [FuentesController::class, 'delete'])->where('id', '[0-9]+');

    //rutas observaciones
    Route::post('observacion', [ObservacionesController::class, 'create']);
    Route::put('observacion', [ObservacionesController::class, 'update']);
    Route::delete('observacion/{id}', [ObservacionesController::class, 'delete'])->where('id', '[0-9]+');

    //rutas problemas
    Route::post('problema', [ProblemasOportunidadesController::class, 'create']);
    Route::put('problema', [ProblemasOportunidadesController::class, 'update']);
    Route::delete('problema/{id}', [ProblemasOportunidadesController::class, 'delete'])->where('id', '[0-9]+');

    //rutas recursos
    Route::post('recurso', [RecursosController::class, 'create']);
    Route::put('recurso', [RecursosController::class, 'update']);
    Route::delete('recurso/{id}', [RecursosController::class, 'delete'])->where('id', '[0-9]+');

    //rutas causas-raiz
    Route::post('causes', [CausasRaicesController::class, 'create']);
    Route::put('causes/{cause}', [CausasRaicesController::class, 'update']);
    Route::delete('causes/{cause}', [CausasRaicesController::class, 'delete'])->where('id', '[0-9]+');

    //ruta responsables
    Route::post('responsable', [ResponsablesController::class, 'create']);
    Route::put('responsable', [ResponsablesController::class, 'update']);
    Route::delete('responsable/{id}', [ResponsablesController::class, 'delete'])->where('id', '[0-9]+');

    //ruta evidencias
    Route::post('evidencia', [EvidenciasController::class, 'create']);
    Route::post('evidencias', [EvidenciasController::class, 'createEvidence']);
    Route::get('evidencia/download/{id}', [EvidenciasController::class, 'download'])->where('id', '[0-9]+');
    Route::get('evidencia/view/{id}', [EvidenciasController::class, 'view'])->where('id', '[0-9]+');
    Route::get('evidencia/{id}', [EvidenciasController::class, 'show'])->where('id', '[0-9]+');
    Route::put('evidencia', [EvidenciasController::class, 'update']);
    Route::delete('evidencia/{id}', [EvidenciasController::class, 'delete'])->where('id', '[0-9]+');

    //ruta narrativas
    Route::post('narratives', [NarrativasController::class, 'create']);
    Route::get('narratives/{narrative}', [NarrativasController::class, 'show'])->where('id', '[0-9]+');
    Route::put('narratives/{narrative}', [NarrativasController::class, 'update']);
    Route::delete('narratives/{narratives}', [NarrativasController::class, 'delete'])->where('id', '[0-9]+');
    Route::get('narratives', [NarrativasController::class, 'listNarratives']);
    Route::get('narratives/last/{narrative}', [NarrativasController::class, 'lastNarrative'])->where('id', '[0-9]+');

    //ruta Actas
    Route::post('acts', [ActaController::class, 'createAct']);
    Route::get('acts/{act}', [ActaController::class, 'showAct'])->where('id', '[0-9]+');
    Route::put('acts/{act}', [ActaController::class, 'updateAct'])->where('id', '[0-9]+');
    Route::delete('acts/{act}', [ActaController::class, 'deleteAct'])->where('id', '[0-9]+');
    Route::get('acts', [ActaController::class, 'listActs']);

    Route::post('folder', [FoldersController::class, 'create']);
});
*/