<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Estandar;
use App\Models\Narrativa;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;



class NarrativasController extends Controller
{

    public function create(Request $request)
    {

        $id_user = auth()->user();
        if ($id_user->isAdmin()) {
            $validator = Validator::make($request->all(), [
                "id_estandar" => "required|integer|exists:estandars,id",
                "contenido" => "required",
                "semestre" => [
                    'required',
                    Rule::unique('narrativas', 'semestre')->where(function ($query) use ($request) {
                        return $query->where('id_estandar', $request->id_estandar);
                    }),
                ],
            ]);

            if ($validator->fails()) {
                return response([
                    "status" => "error",
                    "message" => $validator->errors()
                ], 400);
            }

            $narrativa = new Narrativa();
            $narrativa->id_estandar = $request->id_estandar;
            $narrativa->semestre = $request->semestre;
            $narrativa->contenido = $request->contenido;
            $narrativa->save();
            return response([
                "status" => 1,
                "msg" => "!Narrativa creada exitosamente",
                "data" => $narrativa,
            ], 200);
        } else {
            return response([
                "status" => 0,
                "msg" => "No tiene permisos para crear una narrativa",
                "data" => null,
            ], 200);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            "id" => "required|exists:narrativas,id",
            "contenido" => "required",
        ]);
        if (Narrativa::where("id", $request->id)->exists()) {
            $narrativa = Narrativa::find($request->id);
            $narrativa->update([
                "contenido" => $request->contenido,
            ]);
            return response()->json($narrativa, 200);
        } else {
            return response([
                "status" => 0,
                "message" => "!No se encontro la narrativa",
            ], 404);
        }
    }

    public function delete($id)
    {
        if (Narrativa::where("id", $id)->exists()) {
            $narrativa = Narrativa::find($id);
            $narrativa->delete();
            return response([
                "status" => 1,
                "message" => "!Narrativa eliminada",
            ]);
        } else {
            return response([
                "status" => 0,
                "message" => "!No se encontro la narrativa",
            ], 404);
        }
    }

    public function show($id)
    {
        if (Narrativa::where("id", $id)->exists()) {
            $narrativa = Narrativa::find($id);
            return response([
                "status" => 1,
                "message" => "!Narrativa encontrada",
                "data" => $narrativa,
            ]);
        } else {
            return response([
                "status" => 0,
                "message" => "!No se encontro la narrativa",
            ], 404);
        }
    }

    public function listNarrativas()
    {
        $narrativas = Narrativa::all();
        return response([
            "status" => 1,
            "message" => "!Lista de narrativas",
            "data" => $narrativas,
        ]);
    }

    public function ultimaNarrativa(Request $request)
    {
        $request->validate([
            "id_estandar" => 'required|exists:App\Models\Estandar,id',
        ]);
        $narrativa = Narrativa::where("id_estandar", $request->id_estandar)->latest()->first();
        return response([
            "status" => 1,
            "message" => "!Ultima Narrativa del estandar " . $request->id_estandar,
            "data" => $narrativa,
        ]);
    }
}
