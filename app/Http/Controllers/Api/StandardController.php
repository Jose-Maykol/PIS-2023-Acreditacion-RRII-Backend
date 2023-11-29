<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StandardRequest;
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
use App\Models\StandardStatusModel;
use App\Services\EvidenceService;
use App\Services\StandardService;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;

class StandardController extends Controller
{
    protected $standardService;
    protected $evidenceService;

    public function __construct(StandardService $standardService, EvidenceService $evidenceService)
    {

        $this->standardService = $standardService;
        $this->evidenceService = $evidenceService;
    }

    public function createStandard($year, $semester, Request $request)
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
            "status" => 1,
            "msg" => "Estandar creado exitosamente",
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
        try {

            $result = $this->standardService->listPartialStandards($year, $semester);
            return response()->json([
                'status' => 1,
                'message' => 'Lista parcial de estandares',
                'data' => $result
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
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
    public function listStandardsAssignment($year, $semester)
    {
        try {
            $result = $this->standardService->listStandardsAssignment($year, $semester);
            return response()->json([
                "status" => 1,
                "message" => "Lista de estandares",
                "data" => $result,
            ], 200);
        } catch (\App\Exceptions\User\UserNotAuthorizedException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    public function changeStandardAssignment($year, $semester, $standard_id, StandardRequest $request)
    {
        try {
            $request->validated();
            $result = $this->standardService->changeStandardAssignment($standard_id, $request);
            return response()->json([
                'status' => 1,
                'message' => 'Estandares asignados'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 400);
        } catch (\App\Exceptions\User\UserNotAuthorizedException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (\App\Exceptions\Standard\StandardNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
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
    public function showStandardHeader($year, $semester, $standard_id, Request $request)
    {
        try {
            $result = $this->standardService->showStandard($standard_id);
            return response()->json([
                'status' => 1,
                'message' => "Estandar retornado",
                'data' => $result
            ], 200);
        } catch (\App\Exceptions\Standard\StandardNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
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


    public function updateStandardHeader($year, $semester, $standard_id, StandardRequest $request)
    {
        try {
            $request->validated();
            $result = $this->standardService->updateStandardHeader($standard_id, $request);
            return response()->json([
                'status' => 1,
                'message' => 'Estandar modificado exitosamente',
                'data' => $result
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 400);
        } catch (\App\Exceptions\User\UserNotAuthorizedException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (\App\Exceptions\Standard\StandardNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
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

        $idPlan = $request->input('plan_id');

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
            ->select(
                DB::raw("CONCAT('E-', evidences.id) as code"),
                'evidences.id as evidence_id', 
                'evidences.name',
                'evidences.path',
                'evidences.file',
                'evidences.size',
                'evidences.user_id',
                'evidences.plan_id',
                'evidences.folder_id',
                'evidences.evidence_type_id',
                'evidences.standard_id',
                'evidences.date_id',
                'evidences.created_at',
                'evidences.updated_at',
                DB::raw("CONCAT(users.name, ' ', users.lastname) as full_name"))
            ->get();
        $folders = Folder::join('users', 'folders.user_id', '=', 'users.id')
            ->where('folders.parent_id', $parentIdFolder)
            ->where('folders.standard_id', $standardId)
            ->where('folders.evidence_type_id', $idTypeEvidence)
            ->select(
                DB::raw("CONCAT('F-', folders.id) as code"),
                'folders.id as folder_id',
                'folders.path',
                'folders.user_id',
                'folders.parent_id',
                'folders.evidence_type_id',
                'folders.standard_id',
                'folders.date_id',
                'folders.created_at',
                'folders.updated_at',
                DB::raw("CONCAT(users.name, ' ', users.lastname) as full_name"))
            ->get();

        if ($idPlan != null) {
            $evidences = $evidences->where('plan_id', $idPlan);
        }

        if ($evidences->isEmpty() && $folders->isEmpty()) {
            return response()->json([
                "status" => 0,
                "message" => "No se encontraron evidencias",
            ], 404);
        }

        foreach ($evidences as &$evidence) {
            $evidence['extension'] = $evidence['type'];
            unset($evidence['type']);
            $evidence['type'] = 'evidence';
        }

        foreach ($folders as &$folder) {
            $folder['type'] = 'folder';
        }

        return response()->json([
            "status" => 1,
            "data" => [
                "evidences" => $evidences,
                "folders" => $folders,
            ]
        ]);
    }

    public function searchEvidence($year, $semester, $standard_id)
    {
        try{
            $result = $this->evidenceService->searchEvidence($standard_id);
            return response()->json([
                "status" => 1,
                "data" => $result,
            ], 200);
        }
        catch(\App\Exceptions\Standard\StandardNotFoundException $e){
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }
    /*
    public function standardStatus($year, $semester, $standard_id)
    {
        if (StandardModel::where('id', $standard_id)->exists()) {
            $standard= StandardModel::where('id', $standard_id)->select('standard_status_id')->first();
            $standardStatusList = StandardStatusModel::select('id', 'description')->get();
            
            $standardStatusList->each(function ($listItem) use ($standard) {
                $listItem->active = $listItem->id == $standard->standard_status_id;
            });

            return response([
                "status" => 1,
                "data" => $standardStatusList
            ], 200);
        } else {
            return response([
                "status" => 0,
                "message" => "No existe el estándar",
            ], 404);
        }
    }
*/
    public function listStandardStatus($year, $semester, $standard_id = 0)
    {
        try {
            $result = $this->standardService->listStandardStatus($standard_id);
            return response()->json([
                'status' => 1,
                'message' => "Lista de estandares",
                'data' => $result
            ], 200);
        } catch (\App\Exceptions\User\UserNotAuthorizedException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (\App\Exceptions\Standard\StandardNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    public function updateStatusStandard($year, $semester, $standard_id, $standard_status_id, Request $request)
    {
        try {
            $result = $this->standardService->updateStandardStatus($standard_id, $standard_status_id);
            return response()->json([
                'status' => 1,
                'message' => "Estandard actualizado",
            ], 200);
        } catch (\App\Exceptions\User\UserNotAuthorizedException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (\App\Exceptions\Standard\StandardNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (\App\Exceptions\Standard\StandardStatusNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }


    public function listUserAssigned($year, $semester, $standard_id)
    {
        try {
            $result = $this->standardService->listUserAssigned($standard_id);
            return response()->json([
                'status' => 1,
                'message' => "Lista de usuarios del estandar",
                'data' => $result
            ], 200);
        } catch (\App\Exceptions\User\UserNotAuthorizedException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (\App\Exceptions\Standard\StandardNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }
}
