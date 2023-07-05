<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recursos;
use App\Models\plan;
use Illuminate\Http\Request;

class RecursosController extends Controller
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
                $recurso = new Recursos();
                $recurso->id_plan = $request->id_plan;
                $recurso->descripcion = $request->descripcion;
                $recurso->save();
                return response([
                    "status" => 1,
                    "message" => "Recurso creada exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear esta recursos",
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
        if(Recursos::where(["id"=>$request->id])->exists()){
            $recurso = Recursos::find($request->id);
            $plan = plan::find($recurso->id_plan);
            if($plan->id_user == $id_user){                
                $recurso->descripcion = $request->descripcion;
                $recurso->save();
                return response([
                    "status" => 1,
                    "message" => "Recuso actualizada exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar este recuso",
                ],404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro el recurso",
            ],404);
        }
    }

    public function delete($id)
    {
        $id_user = auth()->user()->id;
        if(Recursos::where(["id"=>$id])->exists()){
            $recurso = Recursos::find($id);
            $plan = plan::find($recurso->id_plan);
            if($plan->id_user == $id_user){
                $recurso->delete();
                return response([
                    "status" => 1,
                    "message" => "Recurso eliminada exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar este recuso",
                ],404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro el recurso",
            ],404);
        }
    }
}
