<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
//use App\Models\CausasRaices;
use App\Models\RootCauseModel;
//use App\Models\plan;
use App\Models\PlanModel;
use Illuminate\Http\Request;

use App\Models\RegistrationStatusModel;

class CausasRaicesController extends Controller
{
    /*
		ruta(post): /api/plans/{plan_id}/root-causes
		ruta(post): /api/2023/A/plans/1/root-causes
		datos:
			{
				"id_plan":"1",
                "description":"Este es otro root-causes"
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
                $causa = new RootCauseModel();
                $causa->plan_id = $request->id_plan;
                $causa->description = $request->description;
                $causa->registration_status_id = '1';
                $causa->save();
                return response([
                    "status" => 1,
                    "message" => "Causa creada exitosamente",
                    "data" => $causa
                ], 201); //Recurso creado
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear esta causa",
                ], 403); //Sin permisos
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
		ruta(put): /api/plans/{plan_id}/root-causes/{root_cause_id}
		ruta(put): /api/2023/A/plans/1/root-causes/1
		datos:
			{
				"id_plan":"1"
                "description":"Modificacion de root-causes"
			}
	*/
    public function update(Request $request){
        $request->validate([
            "id_plan"=> "required|integer",
            "description"=> "required"
        ]);
        $id_user = auth()->user()->id;
        if(RootCauseModel::where(["id"=>$request->id_plan])->exists()){
            $causa = RootCauseModel::find($request->id_plan);
            $plan = PlanModel::find($causa->plan_id);
            if($plan->user_id == $id_user){
                $causa->description = $request->description;
                $causa->save();
                return response([
                    "status" => 1,
                    "message" => "Causa actualizada exitosamente",
                    "data" => $causa
                ], 200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar esta causa",
                ], 403); //Sin permisos
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la causa",
            ], 404);
        }
    }

    /*
        ruta(delete): /api/plans/{plan_id}/root-causes/{root_cause_id}
        ruta(delete): /api/2023/A/plans/1/root-causes/1
        datos: {json con los datos qué nos mandan}
    */
    public function delete($year, $semester, $plan_id, $root_cause_id)
    {
        $id_user = auth()->user()->id;
        if(RootCauseModel::where(["id"=>$root_cause_id])->exists()){
            $causa = RootCauseModel::find($root_cause_id);
            $plan = PlanModel::find($causa->plan_id);
            if($plan->user_id == $id_user){
                $causa->delete();
                return response([
                    "status" => 1,
                    "message" => "Causa eliminada exitosamente",
                ], 200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar esta causa",
                ], 403);//Sin permisos
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la casua",
            ], 404);
        }
    }
}
