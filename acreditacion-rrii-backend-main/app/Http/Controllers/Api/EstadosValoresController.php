<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EstadosValores;

class EstadosValoresController extends Controller
{
   public function listEstadosValores(){
        $EstadosValoresList = EstadosValores::all();
        return response([
        "status" => 1,
        "msg" => "!Lista de estados",
        "data" => $EstadosValoresList,
        ]);
   }
}
