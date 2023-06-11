<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CausasRaices;
use App\Models\plan;
use Illuminate\Http\Request;

class CausasRaicesController extends Controller
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
                $causa = new CausasRaices();
                $causa->id_plan = $request->id_plan;
                $causa->descripcion = $request->descripcion;
                $causa->save();
                return response([
                    "status" => 1,
                    "message" => "Causa creada exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear esta causa",
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
        if(CausasRaices::where(["id"=>$request->id])->exists()){
            $causa = CausasRaices::find($request->id);
            $plan = plan::find($causa->id_plan);
            if($plan->id_user == $id_user){
                $causa->descripcion = $request->descripcion;
                $causa->save();
                return response([
                    "status" => 1,
                    "message" => "Causa actualizada exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar esta casua",
                ],404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la casua",
            ],404);
        }
    }

    public function delete($id)
    {
        $id_user = auth()->user()->id;
        if(CausasRaices::where(["id"=>$id])->exists()){
            $causa = CausasRaices::find($id);
            $plan = plan::find($causa->id_plan);
            if($plan->id_user == $id_user){
                $causa->delete();
                return response([
                    "status" => 1,
                    "message" => "Causa eliminada exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar esta causa",
                ],404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la casua",
            ],404);
        }
    }
}
