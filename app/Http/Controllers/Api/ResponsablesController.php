<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use App\Models\plan;
use App\Models\PlanModel;
//use App\Models\Responsables;
use App\Models\ResponsibleModel;

class ResponsablesController extends Controller
{
    /*
		ruta(post): /api/plans/{plan_id}/responsibles
		ruta(post): /api/2023/A/plans/1/responsibles
		datos:
			{
				"id_plan":"1",
                "description":"Este es otro Responsible"
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
                $responsable = new ResponsibleModel();
                $responsable->plan_id = $request->id_plan;
                $responsable->description = $request->description;
                $responsable->registration_status_id = '1';
                $responsable->save();
                return response([
                    "status" => 1,
                    "message" => "Responsable creado exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear responsables",
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
		ruta(put): /api/plans/{plan_id}/responsibles/{responsible_id}
		ruta(put): /api/2023/A/plans/1/responsibles/1
		datos:
			{
				"id_plan":"1"
                "description":"Modificacion de Responsible"
			}
	*/
    public function update(Request $request){
        $request->validate([
            "id_plan"=> "required|integer",
            "description"=> "required"
        ]);
        $id_user = auth()->user()->id;
        if(ResponsibleModel::where(["id"=>$request->id_plan])->exists()){
            $responsable = ResponsibleModel::find($request->id_plan);
            $plan = PlanModel::find($responsable->plan_id);
            if($plan->user_id == $id_user){                
                $responsable->description = $request->description;
                $responsable->save();
                return response([
                    "status" => 1,
                    "message" => "Responsable actualizado exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar responsables",
                ], 404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro al responsable",
            ], 404);
        }
    }

    /*
        ruta(delete): /api/plans/{plan_id}/responsibles/{responsible_id}
        ruta(delete): /api/2023/A/plans/1/responsibles/1
        datos: {json con los datos qué nos mandan}
    */
    public function delete($year, $semester, $plan_id, $responsible_id)
    {
        $id_user = auth()->user()->id;
        if(ResponsibleModel::where(["id"=>$responsible_id])->exists()){
            $responsable = ResponsibleModel::find($responsible_id);
            $plan = PlanModel::find($responsable->plan_id);
            if($plan->user_id == $id_user){
                $responsable->delete();
                return response([
                    "status" => 1,
                    "message" => "Responsable eliminado exitosamente",
                ]);
            }
            else{
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar responsables",
                ], 404);
            }
        }
        else{
            return response([
                "status" => 0,
                "message" => "No se encontro al responsable",
            ], 404);
        }
    }
}
