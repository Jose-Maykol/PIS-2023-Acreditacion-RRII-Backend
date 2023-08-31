<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Observations;
use App\Models\plan;
use Illuminate\Http\Request;

class ObservationsController extends Controller
{
    /*
		ruta(post): /api/plans/{plan}/observations
		ruta(post): /api/plans/30/observations
		datos:
			{
				"id_plan":"30"
                "description":"Problema 1"
                "access_token": "5082e3108d0e4d8cdd948c42102aabd0768fe993b86240569aa5130e373f3b8a"
			}
	*/
    public function create(Request $request) {
        $request->validate([
            "id_plan"=> "required|integer",
            "description"=> "required",
        ]);

        $id_user = auth()->user()->id;
        if(plan::where(["id"=>$request->id_plan])->exists()){
            $plan = plan::find($request->id_plan);
            if($plan->id_user == $id_user){                
                
                $observacion = new Observaciones();
                $observacion->id_plan = $request->id_plan;
                $observacion->description = $request->description;
                $observacion->save();

                return response([
                    "status" => 1,
                    "message" => "Observación creada exitosamente",
                ], 201);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear esta obsevacion",
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

    /*
		ruta(put): /api/plans/{plan}/observations/{observation}
		ruta(put): /api/plans/30/observations/1
		datos:
			{
				"id":"30"
                "description":"Problema 1"
                "access_token": "5082e3108d0e4d8cdd948c42102aabd0768fe993b86240569aa5130e373f3b8a"
			}
	*/
    public function update(Request $request){
        $request->validate([
            "id"=> "required|integer",
            "description"=> "required"
        ]);
        $id_user = auth()->user()->id;
        if(Observaciones::where(["id"=>$request->id])->exists()){
            $observacion = Observaciones::find($request->id);
            $plan = plan::find($observacion->id_plan);
            if($plan->id_user == $id_user){                
                $observacion->description = $request->description;
                $observacion->save();
                return response([
                    "status" => 1,
                    "message" => "Observacion actualizada exitosamente",
                ], 200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar esta observacion",
                ], 404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la observacion",
            ], 404);
        }
    }

    /*
		ruta(delete): /api/plans/{plan}/observations/{observation}
		ruta(delete): /api/plans/30/observations/1
		datos:
			{
				{observation}:"1"
                "access_token": "5082e3108d0e4d8cdd948c42102aabd0768fe993b86240569aa5130e373f3b8a"
			}
	*/
    public function delete($observation)
    {
        $id_user = auth()->user()->id;
        if(Observaciones::where(["id"=>$observation])->exists()){
            $observacion = Observaciones::find($observation);
            $plan = plan::find($observacion->id_plan);
            if($plan->id_user == $id_user){
                $observacion->delete();
                return response([
                    "status" => 1,
                    "message" => "Observacion eliminada exitosamente",
                ], 200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar esta observacion",
                ], 403);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la observacion",
            ], 404);
        }
    }
}
