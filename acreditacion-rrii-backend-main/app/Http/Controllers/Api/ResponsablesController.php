<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\plan;
use App\Models\Responsables;

class ResponsablesController extends Controller
{
    public function create(Request $request) {
        $request->validate([
            "id_plan"=> "required|integer",
            "nombre"=> "required",
        ]);
        $id_user = auth()->user()->id;
        if(plan::where(["id"=>$request->id_plan])->exists()){
            $plan = plan::find($request->id_plan);
            if($plan->id_user == $id_user){                
                $responsable = new Responsables();
                $responsable->id_plan = $request->id_plan;
                $responsable->nombre = $request->nombre;
                $responsable->save();
                return response([
                    "status" => 1,
                    "message" => "Responsable creado exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear responsables",
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
            "nombre"=> "required"
        ]);
        $id_user = auth()->user()->id;
        if(Responsables::where(["id"=>$request->id])->exists()){
            $responsable = Responsables::find($request->id);
            $plan = plan::find($responsable->id_plan);
            if($plan->id_user == $id_user){                
                $responsable->nombre = $request->nombre;
                $responsable->save();
                return response([
                    "status" => 1,
                    "message" => "Responsable actualizado exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar responsables",
                ],404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro al responsable",
            ],404);
        }
    }

    public function delete($id)
    {
        $id_user = auth()->user()->id;
        if(Responsables::where(["id"=>$id])->exists()){
            $responsable = Responsables::find($id);
            $plan = plan::find($responsable->id_plan);
            if($plan->id_user == $id_user){
                $responsable->delete();
                return response([
                    "status" => 1,
                    "message" => "Responsable eliminado exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar responsables",
                ],404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro al responsable",
            ],404);
        }
    }
}
