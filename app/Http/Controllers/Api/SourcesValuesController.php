<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SourcesValues;

class SourcesValuesController extends Controller
{
    /*
		ruta(get): localhost:8000/api/2023/A/values/sources
		ruta(get): localhost:8000/api/2023/A/values/sources
		datos:
			{
                "access_token": "5082e3108d0e4d8cdd948c42102aabd0768fe993b86240569aa5130e373f3b8a"
			}
	*/
    public function listSourcesValues()
    {
        try {
            $SourceValueList = SourcesValues::all();

            $response = [
                'status' => 1,
                'msg' => 'Lista de fuentes y valores obtenida exitosamente.',
                'data' => SourcesValuesController::collection($SourceValueList),
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
