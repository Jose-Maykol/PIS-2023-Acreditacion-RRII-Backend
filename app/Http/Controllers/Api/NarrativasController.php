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
        /*
            ruta(post): /api/standard/{standard}/narratives
            ruta(post): /api/standard/1/narratives
            datos: {json con los datos qué nos mandan}
        */
        $id_user = auth()->user();
        if ($id_user->isAdmin()) {
            $validator = Validator::make($request->all(), [
                "id_estandar" => "required|integer|exists:estandars,id",
                "content" => "required",
                "semester" => [
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
                ], 400); //el servidor no pudo interpretar la solicitud dada una sintaxis inválida.
            }

            $narrativa = new Narrativa();
            $narrativa->id_estandar = $request->id_estandar;
            $narrativa->semester = $request->semester;
            $narrativa->content = $request->content;
            $narrativa->save();
            return response([
                "status" => 1,
                "message" => "!Narrativa creada exitosamente",
                "data" => $narrativa,
            ], 201); //Recurso creado
        } else {
            return response([
                "status" => 0,
                "message" => "No tiene permisos para crear una narrativa",
                "data" => null,
            ], 403); //Sin permisos
        }
    }

    public function update(Request $request)
    {
        /*
            ruta(put): /api/standard/{standard}/narratives/{narrative}
            ruta(put): /api/standard/1/narratives/2
            datos: {json con los datos qué nos mandan}
        */
        $request->validate([
            "id" => "required|exists:narrativas,id",
            "content" => "required",
        ]);
        if (Narrativa::where("id", $request->id)->exists()) {
            $narrativa = Narrativa::find($request->id);
            $narrativa->update([
                "content" => $request->content,
            ]);
            return response()->json($narrativa, 200);
        } else {
            return response([
                "status" => 0,
                "message" => "!No se encontro la narrativa",
            ], 404);
        }
    }

    public function delete($narrative)
    {
        /*
            ruta(delete): /api/standard/{standard}/narratives/{narrative}
            ruta(delete): /api/standard/1/narratives/2
            datos: {json con los datos qué nos mandan}
        */
        if (Narrativa::where("id", $narrative)->exists()) {
            $narrativa = Narrativa::find($narrative);
            $narrativa->delete();
            return response([
                "status" => 1,
                "message" => "!Narrativa eliminada",
            ],200);
        } else {
            return response([
                "status" => 0,
                "message" => "!No se encontro la narrativa",
            ], 404);
        }
    }

    public function show($narrative)
    {
        /*
            ruta(get): /api/standard/{standard}/narratives/{narrative}
            ruta(get): /api/standard/1/narratives/2
            datos: {json con los datos qué nos mandan}
        */
        if (Narrativa::where("id", $narrative)->exists()) {
            $narrativa = Narrativa::find($narrative);
            return response([
                "status" => 1,
                "message" => "!Narrativa encontrada",
                "data" => $narrativa,
            ],200);
        } else {
            return response([
                "status" => 0,
                "message" => "!No se encontro la narrativa",
            ], 404);
        }
    }

    public function listNarratives()
    {
        /*
            ruta(get): /api/standard/{standard}/narratives
            ruta(get): /api/standard/1/narratives
            datos: {json con los datos qué nos mandan}
        */
        $narrativas = Narrativa::all();
        return response([
            "status" => 1,
            "message" => "!Lista de narrativas",
            "data" => $narrativas,
        ],200);
    }

    public function lastNarrative(Request $request)
    {
        /*
            ruta(get): /api/standard/{standard}/narratives/last
            ruta(get): /api/standard/1/narratives/last/1
            datos: {json con los datos qué nos mandan}
        */
        $request->validate([
            "id_estandar" => 'required|exists:App\Models\Estandar,id',
        ]);
        $narrativa = Narrativa::where("id_estandar", $request->id_estandar)->latest()->first();
        return response([
            "status" => 1,
            "message" => "!Ultima Narrativa del estandar " . $request->id_estandar,
            "data" => $narrativa,
        ],200);
    }
}
