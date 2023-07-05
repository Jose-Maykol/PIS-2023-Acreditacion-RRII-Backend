<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evidencias;
use App\Models\plan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Illuminate\Http\Request;



class EvidenciasController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            "id_plan" => "required|integer",
            "codigo" => "required",
            "denominacion" => "required",
            "adjunto" => "required",
        ]);
        $id_user = auth()->user();
        if (plan::where(["id" => $request->id_plan])->exists()) {
            $plan = plan::find($request->id_plan);
            if ($id_user->isCreadorPlan($request->id_plan) or $id_user->isAdmin()) {
                $evidencia = new Evidencias();
                $evidencia->id_plan = $request->id_plan;
                $evidencia->codigo = $plan->codigo;
                $evidencia->denominacion = $request->denominacion.'.'.$request->adjunto->extension();
                $path = $request->adjunto->storePubliclyAs(
                    'evidencias',
                    $request->adjunto->getClientOriginalName()
                );
                error_log($path);

                $evidencia->adjunto = $path;
                $evidencia->id_user = $id_user->id;
                $evidencia->save();
                return response([
                    "status" => 1,
                    "message" => "Evidencia creada exitosamente",
                    "evidencia" => $evidencia
                ]);
            } else {
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para crear esta Evidencia",
                ], 404);
            }
        } else {
            return response([
                "status" => 0,
                "message" => "No se encontro el plan",
            ], 404);
        }
    }

    public function show($id)
    {
        if (Evidencias::where("id", $id)->exists()) {
            $evidencia = Evidencias::find($id);
            //Para retornar nombre de user
            /*$user = User::find($evidencia->id_user);
			$evidencia->id_user = $user->name;*/
            return response([
                "status" => 1,
                "msg" => "!Evidencia",
                "data" => $evidencia,
            ]);
        } else {
            return response([
                "status" => 0,
                "msg" => "!No se encontro el evidencia",
            ], 404);
        }
    }





    public function update(Request $request)
    {
        $request->validate([
            "id" => "required|integer",
            "codigo" => "required",
            "denominacion" => "required",
            "adjunto" => "required"
        ]);
        $id_user = auth()->user();
        if (Evidencias::where(["id" => $request->id])->exists()) {
            $evidencia = Evidencias::find($request->id);
            $plan = plan::find($evidencia->id_plan);
            if ($id_user->isCreadorPlan($plan->id) or $id_user->isAdmin()) {
                $evidencia->codigo = $request->codigo;
                $evidencia->denominacion = $request->denominacion.$request->adjunto->extension();
                $path = $request->adjunto->storePubliclyAs(
                    'evidencias',
                    $request->adjunto->getClientOriginalName()
                );
                $evidencia->adjunto = $path;
                $evidencia->save();

                return response([
                    "status" => 1,
                    "message" => "Evidencia actualizada exitosamente",
                ]);
            } else {
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para actualizar esta evidencia",
                ], 404);
            }
        } else {
            return response([
                "status" => 0,
                "message" => "No se encontro la evidencia",
            ], 404);
        }
    }

    public function delete($id)
    {
        $id_user = auth()->user();
        if (Evidencias::where(["id" => $id])->exists()) {
            $evidencia = Evidencias::find($id);
            $plan = plan::find($evidencia->id_plan);
            if ($id_user->isCreadorPlan($plan->id) or $id_user->isAdmin()) {
                $evidencia->delete();
                return response([
                    "status" => 1,
                    "message" => "Evidencia eliminada exitosamente",
                ]);
            } else {
                return response([
                    "status" => 0,
                    "message" => "No tienes permisos para eliminar esta evidencia",
                ], 404);
            }
        } else {
            return response([
                "status" => 0,
                "message" => "No se encontro la evidencia",
            ], 404);
        }
    }

    public function download($id)
    {
        if (Evidencias::where("id", $id)->exists()) {
            $evidencia = Evidencias::find($id);
            $path = storage_path('app/' . $evidencia->adjunto);
            //$evidencia->adjunto = download($path);
            return response()->download($path);
        } else {
            return response([
                "status" => 0,
                "msg" => "!No se encontro la evidencia",
            ], 404);
        }
    }
}
