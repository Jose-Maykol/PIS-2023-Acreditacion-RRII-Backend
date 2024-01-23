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

Route::prefix('auth')->group(function () {
    Route::post('', [LoginController::class, 'login']);
    Route::get('{provider}', [LoginController::class, 'redirectToProvider']);
    Route::get('{provider}/callback', [LoginController::class, 'handleProviderCallback']);
});

Route::prefix('test')->group(function () {
    /*Route::get('log', function (Request $request) {
        $token = User::find(1)->createToken('Token Name')->plainTextToken;
        return response()->json(['token' => $token]);
    });*/
    Route::get('goal', function (Request $request) {

        $user = User::find(2);
        $role = $user->hasRole('administrador') ? 'administrador' : 'docente';
        return $role;
    }); //->where(['year' => '\d{4}']);
});

Route::middleware("auth:sanctum")->prefix('auth')->group(function () {
    //Rutas de Auth
    Route::get('logout', [LoginController::class, 'logout']);
});
