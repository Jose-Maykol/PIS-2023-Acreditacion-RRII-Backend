<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FuentesValores;

class FuentesValoresController extends Controller
{
    
    public function listSourcesValues()
    {
        try {
            $SourceValueList = FuentesValores::all();

            $response = [
                'status' => 1,
                'msg' => 'Lista de fuentes y valores obtenida exitosamente.',
                'data' => FuentesValoresResource::collection($SourceValueList),
            ];

            return response()->json($response, 200);

        } catch (\Exception $e) {
            // En caso de error, retornar una respuesta adecuada
            $errorResponse = [
                'status' => 0,
                'msg' => 'Hubo un error al obtener la lista de fuentes y valores.',
                'error' => $e->getMessage(),
            ];

            return response()->json($errorResponse, 500);
        }

    }

}
