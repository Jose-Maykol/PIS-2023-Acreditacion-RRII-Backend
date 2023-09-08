<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Models\GoalModel;
use App\Models\PlanModel;
use App\Models\RegistrationStatusModel;

//Rutas de Auth

Route::prefix('auth')->group(function (){
    
    Route::post('login', [LoginController::class, 'login']);
    Route::get('login/{provider}', [LoginController::class, 'redirectToProvider']);
    Route::get('login/{provider}/callback', [LoginController::class, 'handleProviderCallback']);

});

Route::prefix('test')->group(function(){
        
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