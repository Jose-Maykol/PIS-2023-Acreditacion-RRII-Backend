<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
//use App\Models\ProblemsOpportunities;
use App\Models\ProblemOpportunityModel;
//use App\Models\plan;
use App\Models\PlanModel;
use Illuminate\Http\Request;

class ProblemsOpportunitiesController extends Controller
{
    /*
		ruta(post): /api/plans/{plan_id}/problems-opportunities
		ruta(post): /api/2023/A/plans/1/problems-opportunities
		datos:
			{
				"id_plan":"1",
                "description":"Este es otro problema oportunidad"
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
                $problema = new ProblemOpportunityModel();
                $problema->plan_id = $request->id_plan;
                $problema->description = $request->description;
                $problema->registration_status_id = '1';
                $problema->save();
                return response([
                    "status" => 1,
                    "message" => "Problema opoortunidad creada exitosamente",
                ], 201);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear esta problema oportunidad",
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
		ruta(put): /api/plans/{plan_id}/problems-opportunities/{problem_opportunitie_id}
		ruta(put): /api/2023/A/plans/1/problems-opportunities/1
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
        if(ProblemOpportunityModel::where(["id"=>$request->id_plan])->exists()){
            $problema = ProblemOpportunityModel::find($request->id_plan);
            $plan = PlanModel::find($problema->plan_id);
            if($plan->user_id == $id_user){                
                $problema->description = $request->description;
                $problema->save();
                return response([
                    "status" => 1,
                    "message" => "Problema oportunidad actualizada exitosamente",
                ], 200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar esta problema oportunidad",
                ], 403);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la problema oportunidad",
            ], 404);
        }
    }

    /*
        ruta(delete): /api/plans/{plan_id}/problems-opportunities/{problem_opportunitie_id}
        ruta(delete): /api/2023/A/plans/1/problems-opportunities/1
        datos: {json con los datos qué nos mandan}
    */
    public function delete($year, $semester, $plan_id, $problem_opportunitie_id)
    {
        $id_user = auth()->user()->id;
        if(ProblemOpportunityModel::where(["id"=>$problem_opportunitie_id])->exists()){
            $problema = ProblemOpportunityModel::find($problem_opportunitie_id);
            $plan = PlanModel::find($problema->plan_id);
            if($plan->user_id == $id_user){
                $problema->delete();
                return response([
                    "status" => 1,
                    "message" => "Problema oportunidad eliminada exitosamente",
                ], 200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar esta problema oportunidad",
                ], 403);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la problema oportunidad",
            ], 404);
        }
    }
}
