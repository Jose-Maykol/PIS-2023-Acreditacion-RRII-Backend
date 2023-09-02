<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CausasRaices;
use App\Models\plan;
use Illuminate\Http\Request;

class CausasRaicesController extends Controller
{
    public function create(Request $request) {
        /*
            ruta(post): /api/standard/{standard}/plans/{plan}/causes
            ruta(post): /api/standard/1/plans/1/causes
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
                $causa = new CausasRaices();
                $causa->id_plan = $request->id_plan;
                $causa->description = $request->description;
                $causa->save();
                return response([
                    "status" => 1,
                    "message" => "Causa creada exitosamente",
                    "data" => $causa
                ],201); //Recurso creado
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear esta causa",
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
            ruta(put): /api/standard/{standard}/plans/{plan}/causes/{cause}
            ruta(put): /api/standard/1/plans/2/causes/1
            datos: {json con los datos qué nos mandan}
        */
        $request->validate([
            "id"=> "required|integer",
            "description"=> "required"
        ]);
        $id_user = auth()->user()->id;
        if(CausasRaices::where(["id"=>$request->id])->exists()){
            $causa = CausasRaices::find($request->id);
            $plan = plan::find($causa->id_plan);
            if($plan->id_user == $id_user){
                $causa->description = $request->description;
                $causa->save();
                return response([
                    "status" => 1,
                    "message" => "Causa actualizada exitosamente",
                    "data" => $causa
                ],200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar esta causa",
                ],403); //Sin permisos
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la causa",
            ],404);
        }
    }

    public function delete($cause)
    {
       /*
            ruta(delete): /api/standard/{standard}/plans/{plan}/causes/{cause}
            ruta(delete): /api/standard/1/plans/2/causes/1
            datos: {json con los datos qué nos mandan}
        */
        $id_user = auth()->user()->id;
        if(CausasRaices::where(["id"=>$cause])->exists()){
            $causa = CausasRaices::find($cause);
            $plan = plan::find($causa->id_plan);
            if($plan->id_user == $id_user){
                $causa->delete();
                return response([
                    "status" => 1,
                    "message" => "Causa eliminada exitosamente",
                ],200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar esta causa",
                ],403);//Sin permisos
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
