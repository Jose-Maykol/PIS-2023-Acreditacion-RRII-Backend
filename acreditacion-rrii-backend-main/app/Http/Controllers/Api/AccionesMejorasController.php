<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccionesMejoras;
use App\Models\plan;
use Illuminate\Http\Request;

class AccionesMejorasController extends Controller
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
                $acciones = new AccionesMejoras();
                $acciones->id_plan = $request->id_plan;
                $acciones->descripcion = $request->descripcion;
                $acciones->save();
                return response([
                    "status" => 1,
                    "message" => "Accion de mejora creada exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear esta accion de mejora",
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
        if(AccionesMejoras::where(["id"=>$request->id])->exists()){
            $accion = AccionesMejoras::find($request->id);
            $plan = plan::find($accion->id_plan);
            if($plan->id_user == $id_user){                
                $accion->descripcion = $request->descripcion;
                $accion->save();
                return response([
                    "status" => 1,
                    "message" => "Accion de mejora actualizada exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar esta accion de mejora",
                ],404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la accion de mejora",
            ],404);
        }
    }

    public function delete($id)
    {
        $id_user = auth()->user()->id;
        if(AccionesMejoras::where(["id"=>$id])->exists()){
            $accion = AccionesMejoras::find($id);
            $plan = plan::find($accion->id_plan);
            if($plan->id_user == $id_user){
                $accion->delete();
                return response([
                    "status" => 1,
                    "message" => "Accion de mejora eliminada exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar esta accion de mejora",
                ],404);
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
