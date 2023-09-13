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
        if ($user->isAdmin()) {
            $validator = Validator::make($request->all(), [
                "standard_id" => "required|integer|exists:standards,id",
                "content" => "required",
                "semester" => "required"

            ]);

            if ($validator->fails()) {
                return response([
                    "message" => $validator->errors()
                ], 500); //el servidor no pudo interpretar la solicitud dada una sintaxis inválida.
            }

            $narrative = new NarrativeModel();
            $narrative->standard_id = $request->standard_id;
            $narrative->date_id = DateModel::dateId($year, $semester);
            $narrative->content = $request->content;
            $narrative->registration_status_id = RegistrationStatusModel::registrationActive();

            $narrative->save();
            return response([
                "message" => "!Narrativa creada exitosamente",
                "data" => $narrative,
            ], 201); //Recurso creado
        } else {
            return response([
                "message" => "No tiene permisos para crear una narrativa",
                "data" => null,
            ], 403); //Sin permisos
        }
    }

    public function update($year, $semester, $standard_id, $narrative_id, Request $request)
    {
        /*
            ruta(put): /api/standards/{standard_id}/narratives/{narrative_id}
            ruta(put): /api/2023/A/standards/1/narratives/1
            datos:
            {
                "id":"1",
                "content":"Narrativa de update"
            }
        */
        $request->validate([
            "id" => "required|exists:narratives,id",
            "content" => "required",
        ]);
        if (NarrativeModel::where("id", $narrative_id)->exists()) {
            $narrative = NarrativeModel::find($narrative_id);
            $narrative->update([
                "content" => $request->content,
            ]);
            return response()->json($narrative, 200);
        } else {
            return response([
                "message" => "!No se encontro la narrativa",
            ], 404);
        }
    }

    public function delete($year, $semester, $standard_id, $narrative_id)
    {
        /*
            ruta(delete): /api/standard/{standard}/narratives/{narrative}
            ruta(delete): /api/standard/1/narratives/2
            datos: {json con los datos qué nos mandan}
        */
        if (NarrativeModel::where("id", $narrative_id)->exists()) {
            $narrative = NarrativeModel::find($narrative_id);
            $narrative->deleteRegister();
            return response([
                "message" => "!Narrativa eliminada",
            ], 204); //Sale 204 No Content, no devuelve message
        } else {
            return response([
                "message" => "!No se encontro la narrativa",
            ], 404);
        }
    }

    public function show($year, $semester, $standard_id, $narrative_id)
    {
        /*
            ruta(get): /api/standards/{standard_id}/narratives/{narrative_id}
            ruta(get): /api/standards/1/narratives/1
            datos: {json con los datos qué nos mandan}
        */
        if (NarrativeModel::where("id", $narrative_id)->exists()) {
            $narrative = NarrativeModel::find($narrative_id);
            return response([
                "message" => "!Narrativa encontrada",
                "data" => $narrative,
            ],200);
        } else {
            return response([
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
        $narratives = NarrativeModel::all();
        return response([
            "message" => "!Lista de narrativas",
            "data" => $narratives,
        ],200);
    }

    public function lastNarrative($year, $semester, $standard_id, $narrative_id)
    {
        /*
            ruta(get): /api/standard/{standard}/narratives/last
            ruta(get): /api/standard/1/narratives/last/1
            datos: {json con los datos qué nos mandan}
        */
        /*$request->validate([
            "standard_id" => 'required|exists:App\Models\Standard,id',
        ]);*/
        $narrative = NarrativeModel::where("standard_id", $standard_id)->latest()->first();
        return response([
            "message" => "!Ultima Narrativa del estandar " . $standard_id,
            "data" => $narrative,
        ], 200);
    }
}
