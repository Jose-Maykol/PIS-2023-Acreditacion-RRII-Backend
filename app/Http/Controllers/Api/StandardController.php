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
use PhpParser\PrettyPrinter\Standard;

class EstandarController extends Controller
{
    public function createEstandar(Request $request, $year, $semester)
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
        $standard->user_id = $user->id;
        $standard->name = $request->name;
        $standard->factor = $request->factor;
        $standard->dimension = $request->dimension;
        $standard->related_standards = $request->narelated_standardsme;
        $standard->nro_standard = $request->nro_standard;
        $standard->date_id = DateModel::dateId($year,$semester);
        $standard->registration_status_id = RegistrationStatusModel::registrationActive();

        $standard->save();
        return response([
            "msg" => "!Estandar creado exitosamente",
            "data" => $standard,
        ],201);
    }

    public function listEstandar($year, $semester)
    {
        $standards = StandardModel::where("date_id", DateModel::dateId($year,$semester))
                        ->where('registration_status_id', RegistrationStatusModel::registrationActive())
                        ->get();
        
        if(standards){
            return response([
                "msg" => "!Lista de Estandares",
                "data" => $standards,
            ], 200);
        }
        else {
            return response([
                "msg" => "!No hay lista de Estandares",
            ], 404);
        }
                      
    }

    public function listEstandarValores()
    {
        $standardslist = StandardModel::select('standards.name', 'standards.id', "standards.user_id", "standards.nro_standard", 
                                                "users.name as user_name", "users.lastname as user_lastname", "users.email as user_email")
            ->orderBy('standards.id', 'asc')
            ->join('users', 'standards.user_id', '=', 'users.id')
            ->orderBy('standards.id', 'asc')
            ->get();
        return response([
            "msg" => "!Lista de nombres de Estandares",
            "data" => $standardslist,
        ], );
    }

    public function showEstandar($standard_id)
    {
        if (StandardModel::where("id", $standard_id)
                ->where('registration_status_id', RegistrationStatusModel::registrationActive())
                ->exists()) {
            $standard = StandardModel::find($standard_id);
            $user = UserModel::find($standard->user_id);
            $standard->user = $user;
            $standard->isManager = ($user->id == auth()->user()->id);
            $standard->isAdmin = UserModel::find(auth()->user()->id)->isAdmin();
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

    public function updateEstandar(Request $request,$year, $semester, $standard_id)
    {
        $id_user = auth()->user()->id;
        $user = UserModel::find($id_user);
        if ($user->isEncargadoEstandar($standard_id) || $id_user->isAdmin()) {
            $standard = StandardModel::find($standard_id);
            $standard->name = isset($request->name) ? $request->name : $standard->name;
            $standard->factor = isset($request->factor) ? $request->factor : $standard->factor;
            $standard->dimension = isset($request->dimension) ? $request->dimension : $standard->dimension;
            $standard->related_standards = isset($request->related_standards) ? $request->related_standards : $standard->related_standards;
            $standard->nro_standard = isset($request->nro_standard) ? $request->nro_standard : $standard->nro_standard;
            $standard->date_id = DateModel::where('year', $year)->where('semester', $semester)->get()->id;

            $standard->user_id = isset($request->user_id) ? $request->id_user : $standard->id_user;
            $standard->save();
            return response([
                "msg" => "!Estandar actualizado",
                "data" => $standard,
            ],200);
        } else {
            return response([
                "status" => 0,
                "msg" => "!No se encontro el estandar o no esta autorizado",
            ], 404);
        }
    }

    public function deleteEstandar($standard_id)
    {
        $id_user = auth()->user()->id;
        $user = UserModel::find($id_user);
        if (StandardModel::where(["id" => $standard_id, "user_id" => $user->id])->exists()) {
            $standard = StandardModel::where(["id" => $standard_id, "user_id" => $user->id])->first();
            $standard->deleteRegister();
            return response([
                "msg" => "!Estandar eliminado",
            ], 204);
        } else {
            return response([
                "msg" => "!No se encontro el estandar o no esta autorizado",
            ], 404);
        }
    }

    public function getStandardEvidences(Request $request, $standard_id)
    {
        

        $request->validate([
            'parent_id' => 'nullable|integer',  
        ]);

        $standardId = $standard_id;
        $parentIdFolder = $request->parent_id;
        $idTypeEvidence = $request->query('tipo');

        if (!$request->parent_id) {
            $queryRootFolder = Folder::where('standard_id', $standardId)->where('evidenceType_id', $idTypeEvidence)->where('parent_id', null)->first();
            if ($queryRootFolder == null) {
                return response()->json([
                    "status" => 0,
                    "message" => "Aun no hay evidencias para este estándar",
                ], 404);
            }
            else {
                $parentIdFolder = $queryRootFolder->id;
            }
        }

        $evidences = Evidence::where('folder_id', $parentIdFolder)->where('evidenceType_id', $idTypeEvidence)->where('standard_id', $standardId)->get();
        $folders = Folder::where('parent_id', $parentIdFolder)->where('standard_id', $standardId)->where('evidenceType_id', $idTypeEvidence)->get();

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

    public function getEstandarStructure(Request $request, $id)
    {
        $estandarId = $id;
        $id_tipo = $request->query('tipo');

        return $this->getEstandarFiles($estandarId, $id_tipo);
    }


    public function getEstandarFiles($estandarId, $id_tipo = null)
    {
        $estandarFolderPath = 'evidencias/estandares/' . 'estandar' . $estandarId;
        $fullPath = storage_path('app/' . $estandarFolderPath);

        if (!Storage::exists($estandarFolderPath)) {
            return response()->json([
                "status" => 0,
                "message" => "No se encontró la carpeta del estándar",
            ], 404);
        }
        
        if (!is_dir($fullPath)) {
            return response()->json([
                "status" => 0,
                "message" => "No se encontró la carpeta del estándar",
            ], 404);
        }

        $structure = $this->getFolderStructure($fullPath, $id_tipo);

        return response()->json([
            "status" => 1,
            "message" => "Estructura de archivos y carpetas del estándar obtenida correctamente",
            "structure" => $structure,
        ]);
    }

    private function getFolderStructure($folderPath, $id_tipo = null, $basePath = null)
    {
        if (!$basePath) {
            $basePath = storage_path('app/');
        }

        $structure = [];
        $files = glob($folderPath . '/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                $evidencia = Evidencias::where('adjunto', str_replace($basePath, '', $file))->first();
                if (!$id_tipo || ($evidencia && $evidencia->id_tipo == $id_tipo)) {
                    $user = $evidencia ? UserModel::find($evidencia->id_user) : null;
                    $structure[] = [
                        "type" => "file",
                        "name" => pathinfo($file, PATHINFO_BASENAME),
                        "path" => str_replace($basePath, '', $file),
                        "id_tipo" => $evidencia ? $evidencia->id_tipo : null,
                        "id" => $evidencia ? $evidencia->id : null,
                        "created_at" => $evidencia ? $evidencia->created_at : null,
                        "updated_at" => $evidencia ? $evidencia->updated_at : null,
                        "user" => $user ? $user->name . ' ' . $user->lastname : null,
                    ];
                }
            } elseif (is_dir($file)) {
                $children = $this->getFolderStructure($file, $id_tipo, $basePath);
                if (!empty($children)) {
                    $structure[] = [
                        "type" => "folder",
                        "name" => pathinfo($file, PATHINFO_BASENAME),
                        "path" => str_replace($basePath, '', $file),
                        "children" => $children,
                    ];
                }
            }
        }
        return $structure;
    }
}
