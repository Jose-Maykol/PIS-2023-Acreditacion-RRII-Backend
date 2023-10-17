<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DateModel;
use App\Models\StandardModel;
use App\Models\NarrativeModel;
use App\Models\RegistrationStatusModel;
use App\Models\User;
use Illuminate\Support\Facades\Validator;



class NarrativasController extends Controller
{

    public function create($year, $semester, $standard_id, Request $request)
    {
        /*
            ruta(post): /api/standard/{standard}/narratives
            ruta(post): /api/standard/1/narratives
            datos:
            {
                "standard_id":"1",
                "content":"Narrativa de prueba"
            } 
        */
        $user = auth()->user();
        if ($user->isAdmin() or $user->isAssignStandard($standard_id)) {
            $validator = Validator::make($request->all(), [
                "standard_id" => "required|integer|exists:standards,id",
                "content" => "required",
            ]);

            if ($validator->fails()) {
                return response([
                    "message" => $validator->errors()
                ], 500); //el servidor no pudo interpretar la solicitud dada una sintaxis inválida.
            }

            $standard = StandardModel::find($standard_id);
            $standard->content = $request->content;
            $standard->save();
            return response([
                "message" => "!Narrativa creada exitosamente",
                "data" => $standard,
            ], 201); //Recurso creado
        } else {
            return response([
                "message" => "No tiene permisos para crear una narrativa",
                "data" => null,
            ], 403); //Sin permisos
        }
    }

    public function update($year, $semester, $standard_id, Request $request)
    {
        /*
            ruta(put): /api/standards/{standard_id}/narratives/
            ruta(put): /api/2023/A/standards/1/narratives/
            datos:
            {
                "id":"1",
                "narrative":"Update Narrativa"
            }
        */
        $request->validate([
            "narrative" => "required",
        ]);
        if (StandardModel::where("id", $standard_id)->exists()) {
            $standard = StandardModel::find($standard_id);
            $standard->update([
                "narrative" => $request->narrative,
            ]);
            return response()->json([
                "message" => 'Narrativa correctamente actualizada',
                "standard" => $standard
            ], 200);
        } else {
            return response([
                "message" => "!No se encontro la narrativa",
            ], 404);
        }
    }

    public function delete($year, $semester, $standard_id)
    {
        /*
            ruta(delete): /api/standard/{standard}/narratives/{narrative}
            ruta(delete): /api/standard/1/narratives/2
            datos: {json con los datos qué nos mandan}
        */
        if (StandardModel::where("id", $standard_id)->exists()) {
            $standard = StandardModel::find($standard_id);
            $standard->update([
                "content" => ""
            ]);
            return response([
                "message" => "!Narrativa eliminada",
            ], 204); //Sale 204 No Content, no devuelve message
        } else {
            return response([
                "message" => "!No se encontro la narrativa",
            ], 404);
        }
    }
/*
    public function show($year, $semester, $standard_id)
    {
        
            ruta(get): /api/standards/{standard_id}/narratives/{narrative_id}
            ruta(get): /api/standards/1/narratives/1
            datos: {json con los datos qué nos mandan}
        
        if (StandardModel::where("id", $standard_id)->exists()) {
            $standard = StandardModel::find($standard_id);
            return response([
                "message" => "!Narrativa encontrada",
                "data" => $standard,
            ], 200);
        } else {
            return response([
                "message" => "!No se encontro la narrativa",
            ], 404);
        }
    }

    public function listNarratives()
    {
        
            ruta(get): /api/standard/{standard}/narratives
            ruta(get): /api/standard/1/narratives
            datos: {json con los datos qué nos mandan}
        
        $narratives = NarrativeModel::all();
        return response([
            "message" => "!Lista de narrativas",
            "data" => $narratives,
        ], 200);
    }

    public function lastNarrative($year, $semester, $standard_id, $narrative_id)
    {
        
            ruta(get): /api/standard/{standard}/narratives/last
            ruta(get): /api/standard/1/narratives/last/1
            datos: {json con los datos qué nos mandan}
        
        $request->validate([
            "standard_id" => 'required|exists:App\Models\Standard,id',
        ]);
        $narrative = NarrativeModel::where("standard_id", $standard_id)->latest()->first();
        return response([
            "message" => "!Ultima Narrativa del estandar " . $standard_id,
            "data" => $narrative,
        ], 200);
    }*/
}
