<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
//use App\Models\Recursos;
use App\Models\SourceModel;
//use App\Models\plan;
use App\Models\PlanModel;
use Illuminate\Http\Request;

class RecursosController extends Controller
{
    /*
		ruta(post): /api/plans/{plan_id}/resources
		ruta(post): /api/2023/A/plans/1/resources
		datos:
			{
				"id_plan":"1",
                "description":"Este es otro resource"
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
                $recurso = new SourceModel();
                $recurso->plan_id = $request->id_plan;
                $recurso->descripcion = $request->description;
                $recurso->registration_status_id = '1';
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
                ], 404);
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
		ruta(put): /api/plans/{plan_id}/resources/{resource_id}
		ruta(put): /api/2023/A/plans/1/resources/1
		datos:
			{
				"id_plan":"1"
                "description":"Modificacion de resource"
			}
	*/
    public function update(Request $request){
        $request->validate([
            "id_plan"=> "required|integer",
            "description"=> "required"
        ]);
        $id_user = auth()->user()->id;
        if(SourceModel::where(["id"=>$request->id_plan])->exists()){
            $recurso = SourceModel::find($request->id_plan);
            $plan = PlanModel::find($recurso->plan_id);
            if($plan->user_id == $id_user){                
                $recurso->description = $request->description;
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
                ], 404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro el recurso",
            ], 404);
        }
    }

    /*
        ruta(delete): /api/plans/{plan_id}/resources/{resource_id}
        ruta(delete): /api/2023/A/plans/1/resources/1
        datos: {json con los datos qué nos mandan}
    */
    public function delete($year, $semester, $plan_id, $resource_id)
    {
        $id_user = auth()->user()->id;
        if(SourceModel::where(["id"=>$resource_id])->exists()){
            $recurso = SourceModel::find($resource_id);
            $plan = PlanModel::find($recurso->plan_id);
            if($plan->user_id == $id_user){
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
                ], 404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro el recurso",
            ], 404);
        }
    }
}
