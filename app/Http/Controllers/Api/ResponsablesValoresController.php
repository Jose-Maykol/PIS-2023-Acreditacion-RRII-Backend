<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResponsablesValores;
use Illuminate\Http\Request;

class ResponsablesValoresController extends Controller
{
    /*
		ruta(get): localhost:8000/api/2023/A/values/responsibles
		ruta(get): localhost:8000/api/2023/A/values/responsibles
		datos:
			{
                "access_token": "5082e3108d0e4d8cdd948c42102aabd0768fe993b86240569aa5130e373f3b8a"
			}
	*/
    public function listResponsablesValores()
    {
        
        $ResponsableValorList = ResponsablesValores::all();
        return response([
            "status" => 1,
            "msg" => "!Lista de responsables",
            "data" => $ResponsableValorList,
        ]);
    }

    
}
