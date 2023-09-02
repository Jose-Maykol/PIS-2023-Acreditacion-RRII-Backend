<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccionesMejoras;
use App\Models\plan;
use Illuminate\Http\Request;

class AccionesMejorasController extends Controller
{
    public function create(Request $request) {
        /*
            ruta(post): /api/standard/{standard}/accionesmejora
            ruta(post): /api/standard/1/accionesmejora
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
                $acciones = new AccionesMejoras();
                $acciones->id_plan = $request->id_plan;
                $acciones->description = $request->description;
                $acciones->save();
                return response([
                    "status" => 1,
                    "message" => "Accion de mejora creada exitosamente",
                    'data' => $acciones
                ],201); //Recurso creado
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear esta accion de mejora",
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
            ruta(put): /api/standard/{standard}/accionesmejora/{action}
            ruta(put): /api/standard/1/accionesmejora/2
            datos: {json con los datos qué nos mandan}
        */
        $request->validate([
            "id"=> "required|integer",
            "description"=> "required"
        ]);
        $id_user = auth()->user()->id;
        if(AccionesMejoras::where(["id"=>$request->id])->exists()){
            $accion = AccionesMejoras::find($request->id);
            $plan = plan::find($accion->id_plan);
            if($plan->id_user == $id_user){                
                $accion->description = $request->description;
                $accion->save();
                return response([
                    "status" => 1,
                    "message" => "Accion de mejora actualizada exitosamente",
                    "data" => $accion
                ],200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar esta accion de mejora",
                ],403);//Sin permisos
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la accion de mejora",
            ],404);
        }
    }

    public function delete($action)
    {
        /*
            ruta(delete): /api/standard/{standard}/accionesmejora/{action}
            ruta(delete): /api/standard/1/accionesmejora/2
            datos: {json con los datos qué nos mandan}
        */
        $id_user = auth()->user()->id;
        if(AccionesMejoras::where(["id"=>$action])->exists()){
            $accion = AccionesMejoras::find($action);
            $plan = plan::find($accion->id_plan);
            if($plan->id_user == $id_user){
                $accion->delete();
                return response([
                    "status" => 1,
                    "message" => "Accion de mejora eliminada exitosamente",
                ],200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar esta accion de mejora",
                ],403); //Sin permisos
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la accion de mejora",
            ],404);
        }
    }
}
