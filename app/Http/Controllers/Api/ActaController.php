<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Acta;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ActaController extends Controller
{

    public function createAct(Request $request)
    {
        /*
            ruta(post): /api/standard/{standard}/acts
            ruta(post): /api/standard/1/acts
            datos: {json con los datos qué nos mandan}
        */
		$validator = Validator::make($request->all(), [
            'description' => 'required',
            'date' => 'required',
            'id_estandar' => 'required|exists:estandars,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Se necesita llenar todos los campos',
                'data' => $validator->errors()
            ], 400); //el servidor no pudo interpretar la solicitud dada una sintaxis inválida.
        }

        $user = auth()->user();
        if (!($user->isAdmin() or $user->isEncargadoEstandar($request->id_estandar))) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para crear una acta',
            ], 403); //Sin permisos
        }

        $acta = new Acta();
        $acta->id_estandar = $request->id_estandar;
        $acta->date = $request->date;
        $acta->description = $request->description;
        $acta->save();

        return response()->json([
            'success' => true,
            'message' => 'Acta creada',
            'data' => $acta
        ], 201); //Recurso creado
    }

    public function showAct($act)
    {
        /*
            ruta(get): /api/standard/{standard}/acts/{act}
            ruta(get): /api/standard/1/acts/3
            datos: {json con los datos qué nos mandan}
        */
        $acta = Acta::find($act);
        if ($acta) {
            return response()->json([
                'success' => true,
                'message' => 'Acta encontrada',
                'data' => $acta
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Acta no encontrada',
                'data' => ''
            ], 404);
        }
    }

    public function listActs()
    {
        /*
            ruta(get): /api/standard/{standard}/acts
            ruta(get): /api/standard/1/acts
            datos: {json con los datos qué nos mandan}
        */
        $actas = Acta::all();
        return response()->json([
            'success' => true,
            'message' => 'Actas encontradas',
            'data' => $actas
        ], 200);
    }

    public function updateAct(Request $request)
    {
        /*
            ruta(put): /api/standard/{standard}/acts/{act}
            ruta(put): /api/standard/1/acts/3
            datos: {json con los datos qué nos mandan}
        */
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:actas,id',
            'description' => 'present',
            'date' => 'sometimes',
            'id_estandar' => 'sometimes|exists:estandars,id',
        ]);

        $acta = Acta::find($request->id);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Se produjo un error al actualizar la acta',
                'data' => $validator->errors()
            ], 400);
        }

        if (!$acta) {
            return response()->json([
                'success' => false,
                'message' => 'Acta no encontrada',
                'data' => ''
            ], 404);
        }

        $user = auth()->user();
        if (!($user->isAdmin() or $user->isEncargadoEstandar($request->id_estandar))) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para actualizar una acta',
            ], 403); //Sin permisos
        }

        $acta->description = isset($request->description) ? $request->description : $acta->description;
        $acta->date = isset($request->date) ? $request->date : $acta->date;
        $acta->id_estandar = isset($request->id_estandar) ? $request->id_estandar : $acta->id_estandar;
        $acta->save();

        return response()->json([
            'success' => true,
            'message' => 'Acta actualizada',
            'data' => $acta
        ], 200);
    }

    public function deleteAct($act)
    {
        /*
            ruta(delete): /api/standard/{standard}/acts/{act}
            ruta(delete): /api/standard/1/acts/3
            datos: {json con los datos qué nos mandan}
        */
        $acta = Acta::find($act);
        if (!$acta) {
            return response()->json([
                'success' => false,
                'message' => 'Acta no encontrada',
                'data' => ''
            ], 404);
        }

        $user = auth()->user();
        if (!($user->isAdmin() or $user->isEncargadoEstandar($acta->id_estandar))) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar una acta',
            ], 403); //Sin permisos
        }

        $acta->delete();
        return response()->json([
            'success' => true,
            'message' => 'Acta eliminada',
        ], 200);
    }
}
