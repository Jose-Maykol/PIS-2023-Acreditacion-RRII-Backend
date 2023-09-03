<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EstadosValores;

class EstadosValoresController extends Controller
{
    /*
		ruta(get): localhost:8000/api/2023/A/values/status
		ruta(get): localhost:8000/api/2023/A/values/status
		datos:
			{
                "access_token": "5082e3108d0e4d8cdd948c42102aabd0768fe993b86240569aa5130e373f3b8a"
			}
	*/
   public function listEstadosValores(){
        $EstadosValoresList = EstadosValores::all();
        return response([
            "status" => 1,
            "message" => "!Lista de estados",
            "data" => $EstadosValoresList,
        ], 200);
   }
}
