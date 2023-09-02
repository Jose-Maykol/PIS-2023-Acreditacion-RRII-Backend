<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fuentes;
use App\Models\plan;
use Illuminate\Http\Request;

class FuentesController extends Controller
{
    public function create(Request $request) {
        /*
            ruta(post): /api/standard/{standard}/plans/{plan}/sources
            ruta(post): /api/standard/1/plans/2/sources
            datos: {json con los datos qué nos mandan}
        */
        $request->validate([
            "id_plan"=> "required|integer",
            "description"=> "required",
        ]);
        $id_user = auth()->user()->id;
        if(plan::where(["id"=>$request->id_plan])->exists()){
            $plan = plan::find($request->id_plan);
            if($plan->id_user == $id_user){                
                $fuente = new Fuentes();
                $fuente->id_plan = $request->id_plan;
                $fuente->description = $request->description;
                $fuente->save();
                return response([
                    "status" => 1,
                    "message" => "Fuente creada exitosamente",
                    "data" => $fuente
                ],201);//Recurso creado
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear esta fuente",
                ],403); //Sin permisos
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro el plan",
            ],404);
        }
    }

    public function update(Request $request){
        /*
            ruta(put): /api/standard/{standard}/plans/{plan}/sources/{source}
            ruta(put): /api/standard/1/plans/2/sources/2
            datos: {json con los datos qué nos mandan}
        */
        $request->validate([
            "id"=> "required|integer",
            "description"=> "required"
        ]);
        $id_user = auth()->user()->id;
        if(Fuentes::where(["id"=>$request->id])->exists()){
            $fuente = Fuentes::find($request->id);
            $plan = plan::find($fuente->id_plan);
            if($plan->id_user == $id_user){                
                $fuente->description = $request->description;
                $fuente->save();
                return response([
                    "status" => 1,
                    "message" => "Fuente actualizada exitosamente",
                    "data" => $fuente
                ],200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar esta fuente",
                ],403); //Sin permisos
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la fuente",
            ],404);
        }
    }

    public function delete($sources){
        /*
            ruta(delete): /api/standard/{standard}/plans/{plan}/sources/{source}
            ruta(delete): /api/standard/1/plans/2/sources/2
            datos: {json con los datos qué nos mandan}
        */
        $id_user = auth()->user()->id;
        if(Fuentes::where(["id"=>$sources])->exists()){
            $fuente = Fuentes::find($sources);
            $plan = plan::find($fuente->id_plan);
            if($plan->id_user == $id_user){
                $fuente->delete();
                return response([
                    "status" => 1,
                    "message" => "Fuente eliminada exitosamente",
                ],200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar esta fuente",
                ],403); //Sin permisos
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la fuente",
            ],404);
        }
    }
}
