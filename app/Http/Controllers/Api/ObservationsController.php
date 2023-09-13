<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
//use App\Models\Observations;
use App\Models\ObservationModel;
//use App\Models\plan;
use App\Models\PlanModel;
use Illuminate\Http\Request;

class ObservationsController extends Controller
{
    /*
		ruta(post): /api/plans/{plan_id}/observations
		ruta(post): /api/2023/A/plans/1/observations
		datos:
			{
				"id_plan":"1",
                "description":"Modificacion de observacion"
			}
	*/
    public function create(Request $request) {
        $request->validate([
            "id_plan"=> "required|integer",
            "description"=> "required",
        ]);

        $id_user = auth()->user()->id;
        if(PlanModel::where(["id"=>$request->id_plan])->exists()){
            $plan = PlanModel::find($request->id_plan);
            if($plan->user_id == $id_user){                
                
                $observacion = new ObservationModel();
                $observacion->plan_id = $request->id_plan;
                $observacion->description = $request->description;
                $observacion->registration_status_id = '1';
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
		ruta(put): /api/plans/{plan_id}/observations/{observation_id}
		ruta(put): /api/2023/A/plans/1/observations/1
		datos:
			{
				"id_plan":"1"
                "description":"Esta es otra observacion"
			}
	*/
    public function update(Request $request){
        $request->validate([
            "id_plan"=> "required|integer",
            "description"=> "required"
        ]);
        $id_user = auth()->user()->id;
        if(ObservationModel::where(["id"=>$request->id_plan])->exists()){
            $observacion = ObservationModel::find($request->id_plan);
            $plan = PlanModel::find($observacion->plan_id);
            if($plan->user_id == $id_user){                
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
        ruta(delete): /api/plans/{plan_id}/observations/{observation_id}
        ruta(delete): /api/2023/A/plans/1/observations/1
        datos: {json con los datos qué nos mandan}
    */
    public function delete($year, $semester, $plan_id, $observation_id)
    {
        $id_user = auth()->user()->id;
        if(ObservationModel::where(["id"=>$observation_id])->exists()){
            $observacion = ObservationModel::find($observation_id);
            $plan = PlanModel::find($observacion->plan_id);
            if($plan->user_id == $id_user){
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
