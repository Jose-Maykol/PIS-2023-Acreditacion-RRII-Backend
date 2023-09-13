<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
//use App\Models\Fuentes;
use App\Models\SourceModel;
//use App\Models\plan;
use App\Models\PlanModel;
use Illuminate\Http\Request;

class FuentesController extends Controller
{
    /*
		ruta(post): /api/plans/{plan_id}/sources
		ruta(post): /api/2023/A/plans/1/sources
		datos:
			{
				"id_plan":"1",
                "description":"Este es otro sources"
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
                $fuente = new SourceModel();
                $fuente->plan_id = $request->id_plan;
                $fuente->description = $request->description;
                $fuente->registration_status_id = '1';
                $fuente->save();
                return response([
                    "status" => 1,
                    "message" => "Fuente creada exitosamente",
                    "data" => $fuente
                ], 201);//Recurso creado
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear esta fuente",
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
		ruta(put): /api/plans/{plan_id}/sources/{source_id}
		ruta(put): /api/2023/A/plans/1/sources/1
		datos:
			{
				"id_plan":"1"
                "description":"Modificacion de sources"
			}
	*/
    public function update(Request $request){
        $request->validate([
            "id_plan"=> "required|integer",
            "description"=> "required"
        ]);
        $id_user = auth()->user()->id;
        if(SourceModel::where(["id"=>$request->id_plan])->exists()){
            $fuente = SourceModel::find($request->id_plan);
            $plan = PlanModel::find($fuente->plan_id);
            if($plan->user_id == $id_user){                
                $fuente->description = $request->description;
                $fuente->save();
                return response([
                    "status" => 1,
                    "message" => "Fuente actualizada exitosamente",
                    "data" => $fuente
                ], 200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar esta fuente",
                ], 403); //Sin permisos
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la fuente",
            ], 404);
        }
    }

    /*
        ruta(delete): /api/plans/{plan_id}/sources/{source_id}
        ruta(delete): /api/2023/A/plans/1/sources/1
        datos: {json con los datos qué nos mandan}
    */
    public function delete($year, $semester, $plan_id, $root_cause_id){
        $id_user = auth()->user()->id;
        if(SourceModel::where(["id"=>$root_cause_id])->exists()){
            $fuente = SourceModel::find($root_cause_id);
            $plan = PlanModel::find($fuente->plan_id);
            if($plan->user_id == $id_user){
                $fuente->delete();
                return response([
                    "status" => 1,
                    "message" => "Fuente eliminada exitosamente",
                ], 200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar esta fuente",
                ], 403); //Sin permisos
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la fuente",
            ], 404);
        }
    }
}
