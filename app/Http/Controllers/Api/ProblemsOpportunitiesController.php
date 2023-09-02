<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProblemsOpportunities;
use App\Models\plan;
use Illuminate\Http\Request;

class ProblemsOpportunitiesController extends Controller
{
    /*
		ruta(post): /api/plans/{plan}/problems-opportunities
		ruta(post): /api/plans/30/problems-opportunities
		datos:
			{
				"id_plan":"30"
                "descripcion":"Problema 1"
                "access_token": "5082e3108d0e4d8cdd948c42102aabd0768fe993b86240569aa5130e373f3b8a"
			}
	*/
    public function create(Request $request) {
        $request->validate([
            "id_plan"=> "required|integer",
            "descripcion"=> "required",
        ]);
        $id_user = auth()->user()->id;
        if(plan::where(["id"=>$request->id_plan])->exists()){
            $plan = plan::find($request->id_plan);
            if($plan->id_user == $id_user){                
                $problema = new ProblemasOportunidades();
                $problema->id_plan = $request->id_plan;
                $problema->descripcion = $request->descripcion;
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
		ruta(put): /api/plans/{plan}/problems-opportunities/{problem_opportunitie}
		ruta(put): /api/plans/30/problems-opportunities/{problem_opportunitie}
		datos:
			{
				"id":"30"
                "descripcion":"Problema 1"
                "access_token": "5082e3108d0e4d8cdd948c42102aabd0768fe993b86240569aa5130e373f3b8a"
			}
	*/
    public function update(Request $request){
        $request->validate([
            "id"=> "required|integer",
            "descripcion"=> "required"
        ]);
        $id_user = auth()->user()->id;
        if(ProblemasOportunidades::where(["id"=>$request->id])->exists()){
            $problema = ProblemasOportunidades::find($request->id);
            $plan = plan::find($problema->id_plan);
            if($plan->id_user == $id_user){                
                $problema->descripcion = $request->descripcion;
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
		ruta(delete): /api/plans/{plan}/problems-opportunities/{problem_opportunitie}
		ruta(delete): /api/plans/30/problems-opportunities/1
		datos:
			{
				"problem_opportunitie":"1"
                "access_token": "5082e3108d0e4d8cdd948c42102aabd0768fe993b86240569aa5130e373f3b8a"
			}
	*/
    public function delete($problem_opportunitie)
    {
        $id_user = auth()->user()->id;
        if(ProblemaOportunidad::where(["id"=>$problem_opportunitie])->exists()){
            $problema = ProblemaOportunidad::find($problem_opportunitie);
            $plan = plan::find($problema->id_plan);
            if($plan->id_user == $id_user){
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
