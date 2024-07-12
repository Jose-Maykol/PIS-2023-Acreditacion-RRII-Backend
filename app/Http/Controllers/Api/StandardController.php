<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StandardRequest;
use App\Models\DateModel;
use App\Models\EvidenceModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\StandardModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\RegistrationStatusModel;
use App\Models\StandardStatusModel;
use App\Models\FacultyStaffModel;
use App\Models\FileModel;
use App\Models\FolderModel;
use App\Models\IdentificationContextModel;
use App\Models\UserStandardModel;
use App\Repositories\StandardRepository;
use App\Services\EvidenceService;
use App\Services\StandardService;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

//require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StandardController extends Controller
{
    protected $standardService;
    protected $evidenceService;
    protected $standardRepository;

    public function __construct(StandardRepository $standardRepository, StandardService $standardService, EvidenceService $evidenceService)
    {
        $this->standardRepository = $standardRepository;
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
            "message" => "Estándar creado exitosamente",
            "data" => $standard,
        ], 201);
    }

    public function createStandards($year, $semester, Request $request)
    {
        $request->validate([
            '*.name' => 'required',
            '*.factor' => 'required',
            '*.dimension' => 'required',
            '*.related_standards' => 'required',
            '*.nro_standard' => 'required|integer',
            '*.description' => 'nullable|string',
        ]);

        $standards = [];

        $date_id = DateModel::dateId($year, $semester);
        $standardsExists = StandardModel::where('date_id', $date_id)->exists();

        if ($standardsExists) {
            return response([
                "status" => 0,
                "message" => "Ya existen estándares para este periodo",
            ], 400);
        }

        foreach ($request->all() as $standardData) {
            if ($standardData['description'] == null) {
                $standard->description = "";
            }

            $standard = new StandardModel();

            $standard->name = $standardData['name'];
            $standard->factor = $standardData['factor'];
            $standard->dimension = $standardData['dimension'];
            $standard->description = $standardData['description'];
            $standard->related_standards = $standardData['related_standards'];
            $standard->nro_standard = $standardData['nro_standard'];
            $standard->date_id = DateModel::dateId($year, $semester);
            $standard->registration_status_id = RegistrationStatusModel::registrationActiveId();
            $standard->standard_status_id = 1;
            $standard->save();

            $standards[] = $standard;
        }

        return response([
            'status' => 1,
            'message' => 'Estándares creados exitosamente',
            'data' => $standards,
        ], 201);
    }

    public function listStandardHeaders(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'year' => 'required|integer|digits:4',
            'semester' => 'required|string|in:A,B',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Campos requeridos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {

            $year = $request->input('year');
            $semester = $request->input('semester');


            $result = $this->standardService->listStandardHeaders($year, $semester);
            return response()->json([
                'status' => 1,
                'message' => "Estándares obtenidos",
                'data' => $result
            ], 200);
        } catch (\App\Exceptions\User\UserNotAuthorizedException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
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
                'message' => 'Lista parcial de estándares',
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
                "message" => "Lista de estándares",
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
                'message' => 'Estándares asignados'
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
        $result = $this->standardService->showStandard($standard_id);
        return response()->json([
            'status' => 1,
            'message' => "Estándar retornado",
            'data' => $result
        ], 200);
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
                'message' => 'Estándar modificado exitosamente',
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
                "msg" => "Estándar eliminado",
            ], 204);
        } else {
            return response([
                "msg" => "No se encontro el estándar o no esta autorizado",
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

    public function getStandardEvidences(StandardRequest $request, $year, $semester, $standard_id, $evidence_type_id)
    {
        try {
            $result = $this->standardService->getStandardEvidences($request);
            return response()->json([
                "status" => 1,
                "data" => $result,
            ], 200);
        } catch (\App\Exceptions\Evidence\FolderNotFoundException $e) {
            return response()->json([
                "status" => 0,
                "message" => "Aun no hay evidencias para este estándar",
                "data" => [
                    "evidences" => [],
                    "folders" => []
                ]
            ], $e->getCode());
        }
    }

    public function blockNarrative(Request $request)
    {
        try {
            $result = $this->standardService->blockNarrative($request);
            $data = !empty($result['user_name']) ? [
                "status" => 1,
                "message" => "El usuario " . $result['user_name'] . " " . "está editando esta narrativa.",
                "data" => $result
            ] : [
                "status" => 1,
                "data" => $result,
            ];
            return response()->json($data, 200);
        } catch (\App\Exceptions\Standard\NarrativeIsBeingEditingException $e) {
            return response()->json([
                "status" => 0,
                "message" => "La narrativa está siendo editada...",
                "data" => [
                    "user_name" => $e->getMessage()
                ]
            ], $e->getCode());
        }
    }

    public function unlockNarrative(Request $request)
    {
        try {
            $result = $this->standardService->unlockNarrative($request);
            return response()->json([
                "status" => 1,
                "data" => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "No se pudo desbloquear la narrativa.",
            ], $e->getCode());
        }
    }
    public function updateNarrative($year, $semester, $standard_id, Request $request)
    {
        /*
            ruta(put): /api/standards/{standard_id}/narratives/
            ruta(put): /api/2023/A/standards/1/narratives/
            datos:
            {
                "id":"1",
                "narrative":"Update Narrativa"
            }
        */
        $request->validate([
            "narrative" => "present|required",
        ]);
        if (StandardModel::where("id", $standard_id)->exists()) {
            $standard = StandardModel::find($standard_id);
            $standard->update([
                "narrative" => $request->narrative,
            ]);
            $this->standardRepository->unblockNarrative($standard_id, auth()->user()->id);
            return response([
                "status" => 1,
                "message" => "Narrativa actualizada",
            ], 200);
        } else {
            return response([
                "status" => 0,
                "message" => "No se encontro la narrativa",
            ], 404);
        }
    }

    public function enableNarrative($year, $semester, Request $request)
    {
        try {
            $result = $this->standardService->enableNarrative($year, $semester, $request);
            return response()->json([
                "status" => 1,
                "data" => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => $e->getMessage(),
            ], $e->getCode());
        }
    }

    public function searchEvidence($year, $semester, $standard_id)
    {
        try {
            $result = $this->evidenceService->searchEvidence($standard_id);
            return response()->json([
                "status" => 1,
                "data" => $result,
            ], 200);
        } catch (\App\Exceptions\Standard\StandardNotFoundException $e) {
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
