<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return response()->json(['APP' => 'Sistema Gestor de Calidad de la Escuela de Relaciones Industriales'],200);
});
Route::fallback(function(){
    return response()->json(['message' => 'PAGINA NO ENCONTRADA O NO ESTAS AUTORIZADO PARA ESTA PAGINA'], 404);
});