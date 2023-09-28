<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DateModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\StandardModel;
use App\Models\User;
use App\Models\Folder;
use App\Models\Evidence;
use Illuminate\Support\Facades\DB;
use App\Models\Evidencias;
use App\Models\RegistrationStatusModel;
use App\Models\UserModel;
use App\Models\UserStandardModel;
use PhpParser\PrettyPrinter\Standard;

class StandardController extends Controller
{

    public function pruebas(Request $request, $year, $semester, $standard_id)
    {
       
        
    }
    public function createEstandar($year, $semester, Request $request)
    {
        $request->validate([
            "name" => "required",
            "factor" => "required",
            "dimension" => "required",
            "related_standards" => "required",
            "nro_standard" => "required|integer",
        ]);
        $user = auth()->user();
        $standard = new StandardModel();

        $standard->name = $request->name;
        $standard->factor = $request->factor;
        $standard->dimension = $request->dimension;
        $standard->related_standards = $request->related_standards;
        $standard->nro_standard = $request->nro_standard;

        $standard->date_id = DateModel::dateId($year, $semester);
        $standard->registration_status_id = RegistrationStatusModel::registrationActive();

        $standard->save();
        return response([
            "msg" => "!Estandar creado exitosamente",
            "data" => $standard,
        ], 201);
    }

    /*
		ruta(get): localhost:8000/api/2023/A/standards
		ruta(get): localhost:8000/api/2023/A/standards/4/narratives
		datos:
			{
				"access_token":"11|s3NwExv5FWC7tmsqFUfyB48KFTM6kajH7A1oN3u3"
			}
	*/

    public function listPartialStandard($year, $semester)
    {
        $standards = StandardModel::select('id', 'name', 'nro_standard')
            ->where("date_id", DateModel::dateId($year, $semester))
            ->where('registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->orderBy('nro_standard', 'asc')
            ->get();

        if ($standards) {
            return response([
                "msg" => "!Lista parcial de Estandares",
                "data" => $standards,
            ], 200);
        } else {
            return response([
                "msg" => "!No hay lista de Estandares",
            ], 404);
        }
    }
    public function listStandard($year, $semester)
    {
        $standards = StandardModel::where("date_id", DateModel::dateId($year, $semester))
            ->where('registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->orderBy('nro_standard', 'asc')
            ->get();

        if ($standards) {
            return response([
                "msg" => "!Lista de Estandares",
                "data" => $standards,
            ], 200);
        } else {
            return response([
                "msg" => "!No hay lista de Estandares",
            ], 404);
        }
    }
    /*
		ruta(get): localhost:8000/api/2023/A/standards/standard-values
		ruta(get): localhost:8000/api/2023/A/standards/4
		datos:
			{
				"access_token":"11|s3NwExv5FWC7tmsqFUfyB48KFTM6kajH7A1oN3u3"
			}
	*/
    public function listStandardsAssignment($year, $semester){
        $standardslist = StandardModel::where('standards.date_id', DateModel::dateId($year, $semester))
            ->select(
                'standards.name',
                'standards.id',
                'users_standards.user_id',
                'standards.nro_standard',
                'users.name as user_name',
                'users.lastname as user_lastname',
                'users.email as user_email'
            )
            ->leftJoin('users_standards', 'users_standards.standard_id','=', 'standards.id')
            ->leftJoin('users', 'users_standards.user_id', '=', 'users.id')
            ->orderBy('standards.nro_standard', 'asc')
            ->get();
        return response([
            "msg" => "!Lista de nombres de Estandares",
            "data" => $standardslist,
        ], 200);
    }
    public function changeStandardAssignment($year, $semester, $standard_id, Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'users.*' => 'exists:users,id'
        ]);

        $user = auth()->user();
        if($user->isAdmin()){
            $standard = StandardModel::find($standard_id);
            if($standard){
                $standard->users()->sync($request->users);

                return response([
                    "status" => 1,
                    "msg" => "!Asignación de estándar cambiada",
                ], 200);
            }
            else{
                return response([
                    "status" => 0,
                    "msg" => "!No existe el estándar",
                ], 404);
            }
        }
        else{
            return response([
                "status" => 0,
                "msg" => "!No está autorizado",
            ], 403);
        }
    }

    /*
		ruta(get): localhost:8000/api/2023/A/standards/{standard_id}
		ruta(get): localhost:8000/api/2023/A/standards/1
		datos:
			{
				"access_token":"11|s3NwExv5FWC7tmsqFUfyB48KFTM6kajH7A1oN3u3"
			}
	*/
    public function showEstandar($year, $semester, $standard_id, Request $request)
    {

        if (StandardModel::where("id", $standard_id)
            ->where('registration_status_id', RegistrationStatusModel::registrationActive())
            ->exists()
        ) {
            $standard = StandardModel::find($standard_id);
            $user = $standard->users()->first();
            $standard->user = $user;
            $standard->isManager = ($user->id == auth()->user()->id);
            $standard->isAdmin = auth()->user()->isAdmin();
            return response([
                "msg" => "!Estandar",
                "data" => $standard,
            ], 200);
        } else {
            return response([
                "msg" => "!No se encontro el estandar",
            ], 404);
        }
    }

    /*
		ruta(put): localhost:8000/api/2023/A/standards/{standard_id}
		ruta(put): localhost:8000/api/2023/A/standards/1
		datos:
			{
                "name":"E-4 Sostenibilidad(Modificado)",
                "factor":"Uno",
                "dimension":"Uno",
                "related_standards":"dos",
                "nro_standard":"1",
                "access_token":"11|s3NwExv5FWC7tmsqFUfyB48KFTM6kajH7A1oN3u3"
            }
	*/


    public function updateEstandar($year, $semester, $standard_id, Request $request)
    {
        $user = auth()->user();
        
        if ($user->isAssignStandard($standard_id) or $user->isAdmin()) {
            $standard = StandardModel::find($standard_id);
            $standard->name = isset($request->name) ? $request->name : $standard->name;
            $standard->factor = isset($request->factor) ? $request->factor : $standard->factor;
            $standard->dimension = isset($request->dimension) ? $request->dimension : $standard->dimension;
            $standard->related_standards = isset($request->related_standards) ? $request->related_standards : $standard->related_standards;
            $standard->nro_standard = isset($request->nro_standard) ? $request->nro_standard : $standard->nro_standard;
            //$standard->date_id = DateModel::where('year', $year)->where('semester', $semester)->get()->id;
            $standard->save();

            $user_id = isset($request->user_id) ? $request->user_id : $standard->users()->first()->id;
            try{
                $standard = StandardModel::find($standard_id);
                $user_standard = User::find($standard->users()->first()->id);
                $standard->users()->detach($user_standard);
                $standard->users()->attach(User::find($user_id));
                return response([
                    "msg" => "!Estandar actualizado",
                    "data" => $standard,
                ], 200);
            }
            catch(\Exception $e){
                return response([
                    "msg" => "!Error en la Base de datos",
                ], 500);
            }
           
            
        } else {
            return response([
                "status" => 0,
                "msg" => "!No se encontro el estandar o no esta autorizado",
            ], 404);
        }
        
    }

    public function updateUserStandard($year, $semester, $standard_id,Request $request)
    {
        $request->validate([
            "user_id" => "required|integer",
        ]);

        $user = auth()->user();
        if ($user->isAdmin()) {
            $standard = StandardModel::find($standard_id);
            $user_id = isset($request->user_id) ? $request->user_id : $standard->users()->first()->id;
            try{
                $user_standard = User::find($standard->users()->first()->id);
                $standard->users()->detach($user_standard);
                $standard->users()->attach(User::find($user_id));
                return response([
                    "msg" => "!Estandar actualizado",
                    "data" => $standard,
                ], 200);
            }
            catch(\Exception $e){
                return response([
                    "msg" => "!Error en la Base de datos",
                ], 500);
            }
           
            
        } else {
            return response([
                "status" => 0,
                "msg" => "!No se encontro el estandar o no esta autorizado",
            ], 404);
        }
        
    }
    /*
		ruta(delete): localhost:8000/api/2023/A/standards/{standard_id}
		ruta(delete): localhost:8000/api/2023/A/standards/1
		datos:
			{
				"access_token":"11|s3NwExv5FWC7tmsqFUfyB48KFTM6kajH7A1oN3u3"
			}
	*/

    public function deleteEstandar($standard_id)
    {
        //echo 'ID: ' . $standard_id . '';

        $id_user = auth()->user()->id;

        $user = User::find($id_user);
        if (StandardModel::where(["id" => $standard_id, "user_id" => $user->id])->exists()) { //ERROR:  no existe la columna «user_id»
            $standard = StandardModel::where(["id" => $standard_id, "user_id" => $user->id])->first(); //ERROR:  no existe la columna «user_id»
            $standard->deleteRegister(); //función no implementada
            return response([
                "msg" => "!Estandar eliminado",
            ], 204);
        } else {
            return response([
                "msg" => "!No se encontro el estandar o no esta autorizado",
            ], 404);
        }
    }

    /*
		ruta(get): localhost:8000/api/2023/A/standards/{standard_id}/evidencias
		ruta(get): localhost:8000/api/2023/A/standards/1/evidencias
		datos:
			{
				"parent_id":"1",
                "access_token":"11|s3NwExv5FWC7tmsqFUfyB48KFTM6kajH7A1oN3u3"
			}
	*/

    public function getStandardEvidences(Request $request, $year, $semester, $standard_id, $evidence_type_id)
    {
        
        $request->validate([
            'parent_id' => 'nullable|integer',
        ]);

        $standardId = $standard_id;
        $parentIdFolder = $request->parent_id;

        $idTypeEvidence = $evidence_type_id;
        $dateId = DateModel::dateId($year, $semester);

        if (!$request->parent_id) {
            $queryRootFolder = Folder::where('standard_id', $standardId)->where('evidence_type_id', $idTypeEvidence)->where('date_id', $dateId)->where('parent_id', null)->first();
            if ($queryRootFolder == null) {
                return response()->json([
                    "status" => 0,
                    "message" => "Aun no hay evidencias para este estándar",
                ], 404);
            } else {
                $parentIdFolder = $queryRootFolder->id;
            }
        }

        $evidences = Evidence::join('users', 'evidences.user_id', '=', 'users.id')
            ->where('evidences.folder_id', $parentIdFolder)
            ->where('evidences.evidence_type_id', $idTypeEvidence)
            ->where('evidences.standard_id', $standardId)
            ->select('evidences.*', DB::raw("CONCAT(users.name, ' ', users.lastname) as full_name"))
            ->get();
        $folders = Folder::join('users', 'folders.user_id', '=', 'users.id')
            ->where('folders.parent_id', $parentIdFolder)
            ->where('folders.standard_id', $standardId)
            ->where('folders.evidence_type_id', $idTypeEvidence)
            ->select('folders.*', DB::raw("CONCAT(users.name, ' ', users.lastname) as full_name"))
            ->get();

        if ($evidences->isEmpty() && $folders->isEmpty()) {
            return response()->json([
                "status" => 0,
                "message" => "No se encontraron evidencias",
            ], 404);
        }

        return response()->json([
            "status" => 1,
            "message" => "Evidencias obtenidas correctamente",
            "evidences" => $evidences,
            "folders" => $folders,
        ]);
    }

    public function searchEvidence($year, $semester, $standard_id)
    {   
        if (StandardModel::where("id", $standard_id)->exists()) {
            $evidences = Evidence::where('standard_id', $standard_id)->select('id', 'name', 'file', 'type') ->get();
            return response()->json([
                "status" => 0,
                "data" => $evidences,
            ], 200);
        } else {
            return response()->json([
                "status" => 0,
                "message" => "No existe el estandar",
            ], 404);
        }
    }
}