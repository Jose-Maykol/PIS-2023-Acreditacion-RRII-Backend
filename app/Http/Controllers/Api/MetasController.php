<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
//use App\Models\Metas;
use App\Models\GoalModel;
//use App\Models\plan;
use App\Models\PlanModel;
use Illuminate\Http\Request;

class MetasController extends Controller {
    
    /*
		ruta(post): localhost:8000/api/2023/A/plans/{plan_id}/goals
		ruta(post): localhost:8000/api/2023/A/plans/1/goals
		datos:
            {
                "id_plan":"1",
                "descripcion":"Meta1"
            }
	*/
    public function create(Request $request) {
        $request->validate([
            "id_plan"=> "required|integer",
            "descripcion"=> "required",
        ]);
        $id_user = auth()->user()->id;
        if(PlanModel::where(["id"=>$request->id_plan])->exists()){
            $plan = PlanModel::find($request->id_plan);
            //echo 'ID: ' . $plan . '';
            //echo '$plan->user_id: ' . $plan->user_id . '';
            //echo '$id_user: ' . $id_user . '';
            if($plan->user_id == $id_user){                
                $meta = new GoalModel();
                $meta->plan_id = $request->id_plan;
                $meta->description = $request->descripcion;
                $meta->registration_status_id = '1';
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
		ruta(put): localhost:8000/api/2023/A/plans/{plan_id}/goals/{goal_id}
		ruta(put): localhost:8000/api/2023/A/plans/1/goals/1
		datos:
            {
                "id_plan":"1",
                "descripcion":"Meta3 Cambiada"
            }
	*/
    public function update(Request $request){
        $request->validate([
            "id"=> "required|integer",
            "descripcion"=> "required"
        ]);
        //
        $id_user = auth()->user()->id;
        if(GoalModel::where(["id"=>$request->id])->exists()){
            $meta = GoalModel::find($request->id);
            $plan = PlanModel::find($meta->plan_id);
            if($plan->user_id == $id_user){                
                $meta->description = $request->descripcion;
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
                ], 404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la meta",
            ], 404);
        }
    }

    public function delete($year, $semester, $plan_id, $goal_id){
        $id_user = auth()->user()->id;
        if(GoalModel::where(["id"=>$goal_id])->exists()){
            $meta = GoalModel::find($goal_id);
            $plan = PlanModel::find($meta->plan_id);
            if($plan->user_id == $id_user){
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
                ], 404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la meta",
            ], 404);
        }
    }
}
