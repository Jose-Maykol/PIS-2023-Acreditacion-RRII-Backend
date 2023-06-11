<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FuentesValores;

class FuentesValoresController extends Controller
{
    
    public function listFuentesValores()
    {
        
        $FuenteValorList = FuentesValores::all();
        return response([
            "status" => 1,
            "msg" => "!Lista de fuentes",
            "data" => $FuenteValorList,
        ]);
    }

}
