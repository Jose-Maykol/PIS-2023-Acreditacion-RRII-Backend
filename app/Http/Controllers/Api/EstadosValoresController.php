<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EstadosValores;

class EstadosValoresController extends Controller
{
   public function listEstadosValores(){
        /*
            ruta(get): /api/estados
            ruta(get): /api/estados
            datos: {json con los datos qué nos mandan}
        */
        $EstadosValoresList = EstadosValores::all();
        return response([
            "status" => 1,
            "message" => "!Lista de estados",
            "data" => $EstadosValoresList,
        ],200);
   }
}
