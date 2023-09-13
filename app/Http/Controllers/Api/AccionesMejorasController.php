<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
//use App\Models\AccionesMejoras;
use App\Models\ImprovementActionModel;
//use App\Models\plan;
use App\Models\PlanModel;
use Illuminate\Http\Request;

class AccionesMejorasController extends Controller
{
    /*
        ruta(post): localhost:8000/api/2023/A/plans/{plan_id}/improvement-actions
        ruta(post): localhost:8000/api/2023/A/plans/1/improvement-actions
        datos:
            {
                "id_plan":"1",
                "description":"esta es segunda acción de mejora del plan de mejora 1"
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
                $acciones = new ImprovementActionModel();
                $acciones->plan_id = $request->id_plan;
                $acciones->description = $request->description;
                $acciones->registration_status_id = '1';
                $acciones->save();
                return response([
                    "status" => 1,
                    "message" => "Accion de mejora creada exitosamente",
                    'data' => $acciones
                ], 201); //Recurso creado
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear esta accion de mejora",
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
        ruta(post): localhost:8000/api/2023/A/plans/{plan_id}/improvement-actions
        ruta(post): localhost:8000/api/2023/A/plans/1/improvement-actions
        datos:
            {
                "id_plan":"1",
                "description":"esta es segunda acción de mejora del plan de mejora 1"
            }
    */
    public function update(Request $request){
        $request->validate([
            "id"=> "required|integer",
            "description"=> "required"
        ]);
        $id_user = auth()->user()->id;
        if(ImprovementActionModel::where(["id"=>$request->id])->exists()){
            $accion = ImprovementActionModel::find($request->id);
            $plan = PlanModel::find($accion->plan_id);
            if($plan->user_id == $id_user){                
                $accion->description = $request->description;
                $accion->save();
                return response([
                    "status" => 1,
                    "message" => "Accion de mejora actualizada exitosamente",
                    "data" => $accion
                ], 200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar esta accion de mejora",
                ], 403);//Sin permisos
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la accion de mejora",
            ], 404);
        }
    }

    /*
        ruta(delete): /api/2023/A/plans/{plan_id}/improvement-actions/{improvement_action_id}
        ruta(delete): /api/2023/A/plans/1/improvement-actions/1
        datos: {json con los datos qué nos mandan}
    */
    public function delete($year, $semester, $plan_id, $improvement_action_id)
    {
        $id_user = auth()->user()->id;
        if(ImprovementActionModel::where(["id"=>$improvement_action_id])->exists()){
            $accion = ImprovementActionModel::find($improvement_action_id);
            $plan = PlanModel::find($accion->plan_id);
            if($plan->user_id == $id_user){
                $accion->delete();
                return response([
                    "status" => 1,
                    "message" => "Accion de mejora eliminada exitosamente",
                ], 200);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar esta accion de mejora",
                ], 403); //Sin permisos
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro la accion de mejora",
            ], 404);
        }
    }
}
