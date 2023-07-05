<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResponsablesValores;
use Illuminate\Http\Request;

class ResponsablesValoresController extends Controller
{
    
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
