<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fuentes;
use App\Models\plan;
use Illuminate\Http\Request;

class FuentesController extends Controller
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
                $fuente = new Fuentes();
                $fuente->id_plan = $request->id_plan;
                $fuente->descripcion = $request->descripcion;
                $fuente->save();
                return response([
                    "status" => 1,
                    "message" => "Fuente creada exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear esta fuente",
                ],404);
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
        $request->validate([
            "id"=> "required|integer",
            "descripcion"=> "required"
        ]);
        $id_user = auth()->user()->id;
        if(Fuentes::where(["id"=>$request->id])->exists()){
            $fuente = Fuentes::find($request->id);
            $plan = plan::find($fuente->id_plan);
            if($plan->id_user == $id_user){                
                $fuente->descripcion = $request->descripcion;
                $fuente->save();
                return response([
                    "status" => 1,
                    "message" => "Fuente actualizada exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar esta fuente",
                ],404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la fuente",
            ],404);
        }
    }

    public function delete($id)
    {
        $id_user = auth()->user()->id;
        if(Fuentes::where(["id"=>$id])->exists()){
            $fuente = Fuentes::find($id);
            $plan = plan::find($fuente->id_plan);
            if($plan->id_user == $id_user){
                $fuente->delete();
                return response([
                    "status" => 1,
                    "message" => "Fuente eliminada exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar esta fuente",
                ],404);
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
