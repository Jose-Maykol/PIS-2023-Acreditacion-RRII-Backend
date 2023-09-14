<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\EvidenciasController;
use App\Models\GoalModel;
use App\Models\PlanModel;
use App\Models\RegistrationStatusModel;
use App\Models\User;

//Rutas de Auth

Route::prefix('auth')->group(function (){
    
    Route::post('login', [LoginController::class, 'login']);
    Route::get('login/{provider}', [LoginController::class, 'redirectToProvider']);
    Route::get('login/{provider}/callback', [LoginController::class, 'handleProviderCallback']);

});

Route::get('{year}/{semester}/evidences/{evidence_id}/view', [EvidenciasController::class, 'view'])->where('evidence_id', '[0-9]+');

Route::prefix('test')->group(function(){
        Route::get('log', function (Request $request){
            $token = User::find(1)->createToken('Token Name')->plainTextToken;
            return response()->json(['token' => $token]);
        });
        Route::get('goal', function (Request $request){
            
            //$goal = PlanModel::find(2)->goalsActive();
            $goal = PlanModel::isActived(2);
            //$goal = GoalModel::all()->where('registration_status_id',RegistrationStatusModel::registrationActivo());
            if($goal){
                return PlanModel::find(2)->goalsActive();
            }
            return $goal;
            });//->where(['year' => '\d{4}']);
    });

Route::middleware("auth:sanctum")->prefix('auth')->group(function () {
    //Rutas de Auth
    Route::get('logout', [LoginController::class, 'logout']);
});