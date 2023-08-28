<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProblemasOportunidades;
use App\Models\plan;
use Illuminate\Http\Request;

class ProblemasOportunidadesController extends Controller
{
    public function create(Request $request) {
        $request->validate([
            "id_plan"=> "required|integer",
            "descripcion"=> "required",
        ]);
        $id_user = auth()->user()->id;
        if(plan::where(["id"=>$request->id_plan])->exists()){
            $plan = plan::find($request->id_plan);
            if($plan->id_user == $id_user){                
                $problema = new ProblemasOportunidades();
                $problema->id_plan = $request->id_plan;
                $problema->descripcion = $request->descripcion;
                $problema->save();
                return response([
                    "status" => 1,
                    "message" => "Problema opoortunidad creada exitosamente",
                ], 201);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear esta problema oportunidad",
                ], 403);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro el plan",
            ], 404);
        }
    }

    public function update(Request $request){
        $request->validate([
            "id"=> "required|integer",
            "descripcion"=> "required"
        ]);
        $id_user = auth()->user()->id;
        if(ProblemasOportunidades::where(["id"=>$request->id])->exists()){
            $problema = ProblemasOportunidades::find($request->id);
            $plan = plan::find($problema->id_plan);
            if($plan->id_user == $id_user){                
                $problema->descripcion = $request->descripcion;
                $problema->save();
                return response([
                    "status" => 1,
                    "message" => "Problema oportunidad actualizada exitosamente",
                ], 200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar esta problema oportunidad",
                ], 403);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la problema oportunidad",
            ], 404);
        }
    }

    public function delete($id)
    {
        $id_user = auth()->user()->id;
        if(ProblemaOportunidad::where(["id"=>$id])->exists()){
            $problema = ProblemaOportunidad::find($id);
            $plan = plan::find($problema->id_plan);
            if($plan->id_user == $id_user){
                $problema->delete();
                return response([
                    "status" => 1,
                    "message" => "Problema oportunidad eliminada exitosamente",
                ], 200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar esta problema oportunidad",
                ], 403);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la problema oportunidad",
            ], 404);
        }
    }
}
