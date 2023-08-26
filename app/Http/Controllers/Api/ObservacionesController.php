<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Observaciones;
use App\Models\plan;
use Illuminate\Http\Request;

class ObservacionesController extends Controller
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
                
                $observacion = new Observaciones();
                $observacion->id_plan = $request->id_plan;
                $observacion->descripcion = $request->descripcion;
                $observacion->save();

                return response([
                    "status" => 1,
                    "message" => "Observación creada exitosamente",
                ],201);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear esta obsevacion",
                ],403);
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
        if(Observaciones::where(["id"=>$request->id])->exists()){
            $observacion = Observaciones::find($request->id);
            $plan = plan::find($observacion->id_plan);
            if($plan->id_user == $id_user){                
                $observacion->descripcion = $request->descripcion;
                $observacion->save();
                return response([
                    "status" => 1,
                    "message" => "Observacion actualizada exitosamente",
                ],200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar esta observacion",
                ],404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la observacion",
            ],404);
        }
    }

    public function delete($id)
    {
        $id_user = auth()->user()->id;
        if(Observaciones::where(["id"=>$id])->exists()){
            $observacion = Observaciones::find($id);
            $plan = plan::find($observacion->id_plan);
            if($plan->id_user == $id_user){
                $observacion->delete();
                return response([
                    "status" => 1,
                    "message" => "Observacion eliminada exitosamente",
                ],200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar esta observacion",
                ],403);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la observacion",
            ],404);
        }
    }
}
