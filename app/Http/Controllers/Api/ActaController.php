<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Acta;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ActaController extends Controller
{

    public function create(Request $request)
    {
		$validator = Validator::make($request->all(), [
            'descripcion' => 'required',
            'fecha' => 'required',
            'id_estandar' => 'required|exists:estandars,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Se necesita llenar todos los campos',
                'data' => $validator->errors()
            ], 400);
        }

        $user = auth()->user();
        if (!($user->isAdmin() or $user->isEncargadoEstandar($request->id_estandar))) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para crear una acta',
            ], 401);
        }

        $acta = new Acta();
        $acta->id_estandar = $request->id_estandar;
        $acta->fecha = $request->fecha;
        $acta->descripcion = $request->descripcion;
        $acta->save();

        return response()->json([
            'success' => true,
            'message' => 'Acta creada',
            'data' => $acta
        ], 200);
    }

    public function showActa($id)
    {
        $acta = Acta::find($id);
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

    public function listActas()
    {
        $actas = Acta::all();
        return response()->json([
            'success' => true,
            'message' => 'Actas encontradas',
            'data' => $actas
        ], 200);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:actas,id',
            'descripcion' => 'present',
            'fecha' => 'sometimes',
            'id_estandar' => 'sometimes|exists:estandars,id',
        ]);

        $acta = Acta::find($request->id);
        if (!$acta) {
            return response()->json([
                'success' => false,
                'message' => 'Acta no encontrada',
            ], 404);
        }


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Se produjo un error al actualizar la acta',
                'data' => $validator->errors()
            ], 400);
        }

        $user = auth()->user();
        if (!($user->isAdmin() or $user->isEncargadoEstandar($request->id_estandar))) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para actualizar una acta',
            ], 401);
        }

        $acta->descripcion = isset($request->descripcion) ? $request->descripcion : $acta->descripcion;
        $acta->fecha = isset($request->fecha) ? $request->fecha : $acta->fecha;
        $acta->id_estandar = isset($request->id_estandar) ? $request->id_estandar : $acta->id_estandar;
        $acta->save();

        return response()->json([
            'success' => true,
            'message' => 'Acta actualizada',
            'data' => $acta
        ], 200);
    }

    public function delete($id)
    {
        $acta = Acta::find($id);
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
            ], 401);
        }

        $acta->delete();
        return response()->json([
            'success' => true,
            'message' => 'Acta eliminada',
        ], 200);
    }
}
