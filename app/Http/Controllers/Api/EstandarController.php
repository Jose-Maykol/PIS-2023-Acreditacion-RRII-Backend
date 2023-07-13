<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Estandar;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EstandarController extends Controller
{
    public function createEstandar(Request $request)
    {
        $request->validate([
            "name" => "required",
            "cabecera" => "required",
            "position" => "required|integer",
        ]);
        $id_user = auth()->user()->id;
        $estandar = new Estandar();
        $estandar->id_user = $id_user;
        $estandar->name = $request->name;
        $estandar->cabecera = $request->cabecera;
        $estandar->save();
        return response([
            "status" => 1,
            "msg" => "!Estandar creado exitosamente",
            "data" => $estandar,
        ]);
    }

    public function listEstandar()
    {
        $estandares = Estandar::all();
        return response([
            "status" => 1,
            "msg" => "!Lista de Estandares",
            "data" => $estandares,
        ]);
    }

    public function listEstandarValores()
    {
        $estandareslist = Estandar::select('estandars.name', 'estandars.id', "estandars.id_user", "estandars.position", "users.name as user_name", "users.lastname as user_lastname", "users.email as user_email")
            ->orderBy('estandars.id', 'asc')
            ->join('users', 'estandars.id_user', '=', 'users.id')
            ->orderBy('estandars.id', 'asc')
            ->get();
        return response([
            "status" => 1,
            "msg" => "!Lista de nombres de Estandares",
            "data" => $estandareslist,
        ]);
    }

    public function showEstandar($id)
    {
        if (Estandar::where("id", $id)->exists()) {
            $estandar = Estandar::find($id);
            $user = User::find($estandar->id_user);
            $estandar->user = $user;
            $estandar->esEncargado = ($user->id == auth()->user()->id);
            $estandar->esAdmin = auth()->user()->isAdmin();
            return response([
                "status" => 1,
                "msg" => "!Estandar",
                "data" => $estandar,
            ]);
        } else {
            return response([
                "status" => 0,
                "msg" => "!No se encontro el estandar",
            ], 404);
        }
    }

    public function updateEstandar(Request $request, $id)
    {
        $id_user = auth()->user();
        if ($id_user->isEncargadoEstandar($id) || $id_user->isAdmin()) {
            $estandar = Estandar::find($id);
            $estandar->name = isset($request->name) ? $request->name : $estandar->name;
            $estandar->cabecera = isset($request->cabecera) ? $request->cabecera : $estandar->cabecera;
            $estandar->id_user = isset($request->id_user) ? $request->id_user : $estandar->id_user;
            $estandar->save();
            return response([
                "status" => 1,
                "msg" => "!Estandar actualizado",
                "data" => $estandar,
            ]);
        } else {
            return response([
                "status" => 0,
                "msg" => "!No se encontro el estandar o no esta autorizado",
            ], 404);
        }
    }

    public function deleteEstandar($id)
    {
        $id_user = auth()->user()->id;
        if (Estandar::where(["id" => $id, "id_user" => $id_user])->exists()) {
            $estandar = Estandar::where(["id" => $id, "id_user" => $id_user])->first();
            $estandar->delete();
            return response([
                "status" => 1,
                "msg" => "!Estandar eliminado",
            ]);
        } else {
            return response([
                "status" => 0,
                "msg" => "!No se encontro el estandar o no esta autorizado",
            ], 404);
        }
    }
}
