<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Metas;
use App\Models\plan;
use Illuminate\Http\Request;

class MetasController extends Controller {
    
    public function create(Request $request) {
        $request->validate([
            "id_plan"=> "required|integer",
            "descripcion"=> "required",
        ]);
        $id_user = auth()->user()->id;
        if(plan::where(["id"=>$request->id_plan])->exists()){
            $plan = plan::find($request->id_plan);
            if($plan->id_user == $id_user){                
                $meta = new Metas();
                $meta->id_plan = $request->id_plan;
                $meta->descripcion = $request->descripcion;
                $meta->save();
                return response([
                    "status" => 1,
                    "message" => "Meta creada exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear esta meta",
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
        //
        $id_user = auth()->user()->id;
        if(Metas::where(["id"=>$request->id])->exists()){
            $meta = Metas::find($request->id);
            $plan = plan::find($meta->id_plan);
            if($plan->id_user == $id_user){                
                $meta->descripcion = $request->descripcion;
                $meta->save();
                return response([
                    "status" => 1,
                    "message" => "Meta actualizada exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar esta meta",
                ],404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la meta",
            ],404);
        }
    }

    public function delete($id){
        $id_user = auth()->user()->id;
        if(Metas::where(["id"=>$id])->exists()){
            $meta = Metas::find($id);
            $plan = plan::find($meta->id_plan);
            if($plan->id_user == $id_user){
                $meta->delete();
                return response([
                    "status" => 1,
                    "message" => "Meta eliminada exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar esta meta",
                ],404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la meta",
            ],404);
        }
    }
}
