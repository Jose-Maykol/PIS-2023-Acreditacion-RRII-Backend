<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evidencias;
use App\Models\plan;
use App\Models\User;
use App\Models\StandardModel;
use App\Models\Folder;
use App\Models\Evidence;
use App\Models\DateModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Response as FacadeResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use ZipArchive;



class EvidenciasController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            "id_plan" => "required|integer",
            "id_tipo" => "required|integer",
            "id_estandar" => "required|integer",
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
                $evidencia->id_tipo = $request->id_tipo;
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

    public function createEvidence(Request $request)
    {
        $request->validate([
            "standard_id" => "required|integer",
            "type_evidence_id" => "required|integer",
            "plan_id" => "integer",
            "files" => "required|array",
            "files.*" => "file",
            "path" => "nullable|string",
        ]);

        $userId = auth()->user()->id;
        $year = $request->route('year');
        $semester = $request ->route('semester');
        $dateId = DateModel::dateId($year, $semester);
        $standardId = $request->standard_id;
        $typeEvidenceId = $request->type_evidence_id;
        $planId = $request->has('plan_id')? $request->plan_id : null;
        $generalPath = $request->has('path') ? $request->path : null;
        $parentFolder = null;

        $standardBelongsSemester = StandardModel::where('date_id', $dateId)->where('id', $standardId)->exists();

        if (!$standardBelongsSemester) {
            return response([
                "status" => 0,
                "message" => "No existe este estándar en el periodo " . $year . $semester,
            ], 404);
        }
        
        $folder = Folder::where('path', $generalPath)->where('standard_id', $standardId)->where('evidence_type_id', $typeEvidenceId)->first();
        if (!$folder) {
            $folder = new Folder([
                'name' => $generalPath == '' ? 'root' : $generalPath,
                'user_id' => $userId,
                'path' => $generalPath,
                'standard_id' => $standardId,
                'evidence_type_id' => $typeEvidenceId,
                'date_id' => $dateId,
            ]);
            $folder->save();
        } else {
            $parentFolder = $folder->id;
        }

        foreach ($request->file('files') as $file) {
            if ($file->getClientOriginalExtension() == 'zip') 
            {
                $zip = new ZipArchive;
                if ($zip->open($file) === TRUE) 
                {
                    $extractedPath = storage_path('app/evidencias/'. $year . '/' . $semester . '/' . 'estandar_' . $standardId . '/tipo_evidencia_'. $typeEvidenceId) . '/' . $generalPath;
                    for ($i = 0; $i < $zip->numFiles; $i++) 
                    {
                        $fileInfo = $zip->statIndex($i);
                        $fileName = $generalPath == null ? '/' . trim($fileInfo['name'], '/') : $generalPath . '/' . trim($fileInfo['name'], '/');
                        $isDirectory = substr($fileName, -1) === '/';
                        if ($isDirectory) {
                            if (Folder::where('path', $fileName)->where('standard_id', $standardId)->where('evidence_type_id', $typeEvidenceId)->exists()) {
                                return response([
                                    "status" => 0,
                                    "message" => "Ya existe existe este carpeta",
                                    "file" => $fileName,
                                ], 404);
                            }
                        } else {
                           if (Evidence::where('path', $fileName)->where('standard_id', $standardId)->where('evidence_type_id', $typeEvidenceId)->exists()) {
                                return response([
                                    "status" => 0,
                                    "message" => "Ya existe existe este archivo",
                                    "file" => $fileName,
                                ], 404);
                            }
                        }
                    }

                    $zip->extractTo($extractedPath);
                    $zip->close();
                    $this->createEvidencesAndFolders($extractedPath, $userId, $standardId, $typeEvidenceId, $parentFolder, $planId, $dateId, $year, $semester);

                    return response([
                        "status" => 1,
                        "message" => "Evidencia creada exitosamente",
                    ]);
                }
                else 
                {
                    return response([
                        "status" => 0,
                        "message" => "Error al descomprimir el archivo ZIP",
                    ], 404);
                }
            } 
            else 
            {
                $relativePath = $generalPath == null ? '/' . $file->getClientOriginalName() : $generalPath . '/' . $file->getClientOriginalName();

                if (Evidence::where('path', $relativePath)->where('standard_id', $standardId)->where('evidence_type_id', $typeEvidenceId)->exists()) {
                    return response([
                        "status" => 0,
                        "message" => "Ya existe existe este archivo",
                        "file" => $file->getClientOriginalName(),
                    ], 404);
                }

                $path = $file->storeAs('evidencias/'. $year . '/' . $semester . '/' .'estandar_' . $standardId . '/tipo_evidencia_'. $typeEvidenceId . '/' . $generalPath, $file->getClientOriginalName());

                $evidence = new Evidence([
                    'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'file' => $file->getClientOriginalName(),
                    'type' => $file->getClientOriginalExtension(),
                    'size' => $file->getSize(),
                    'user_id' => $userId,
                    'plan_id' => $planId,
                    'standard_id' => $standardId,
                    'evidence_type_id' => $typeEvidenceId,
                    'path' => $relativePath,
                    'folder_id' => $folder->id,
                    'date_id' => $dateId,
                ]);
                $evidence->save();
                return response([
                    "status" => 1,
                    "message" => "Evidencia creada exitosamente",
                ]);
            }
        }
    }

    private function createEvidencesAndFolders($path, $userId, $standardId, $typeEvidenceId, $parentFolder = null, $planId, $dateId, $year, $semester)
    {   
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $path . '/' . $file;
                $relativePath = str_replace(storage_path('app/'), '', $filePath);
                $basePath = 'evidencias/'. $year . '/' . $semester . '/' . 'estandar_' . $standardId . '/tipo_evidencia_'. $typeEvidenceId . '/';
                $relativePath = str_replace($basePath, '', $relativePath);
                if (is_dir($filePath)) {
                    $folder = new Folder([
                        'name' => $file,
                        'user_id' => $userId,
                        'path' => $relativePath,
                        'standard_id' => $standardId,
                        'evidence_type_id' => $typeEvidenceId,
                        'date_id' => $dateId,
                    ]);
                    if ($parentFolder) {
                        $folder->parent()->associate($parentFolder);
                    }
                    if ($parentFolder == null) {
                        $rootFolder = Folder::where('path', null)->where('standard_id', $standardId)->where('evidence_type_id', $typeEvidenceId)->first();
                        if ($rootFolder) {
                            $folder->parent_id = $rootFolder->id;
                        }
                    }
                    $folder->save();
                    $this->createEvidencesAndFolders($filePath, $userId, $standardId, $typeEvidenceId, $folder, $planId, $dateId, $year, $semester);
                } else {
                    if (Evidence::where('path', $relativePath)->where('standard_id', $standardId)->where('evidence_type_id', $typeEvidenceId)->exists()) {
                        continue;
                    }
                    $fileInfo = pathinfo($filePath);
                    $evidence = new Evidence([
                        'name' => $fileInfo['filename'],
                        'file' => $fileInfo['basename'],
                        'type' => $fileInfo['extension'],
                        'size' => filesize($filePath),
                        'user_id' => $userId,
                        'plan_id' => $planId,
                        'standard_id' => $standardId,
                        'evidence_type_id' => $typeEvidenceId,
                        'path' => $relativePath,
                        'date_id' => $dateId,
                    ]);
                    if ($parentFolder) {
                        $evidence->folder()->associate($parentFolder);
                    }
                    $evidence->save();
                }
            }
        }
    }

    public function update(Request $request, $year, $semester, $evidence_id)
    {
        $request->validate([
            "file" => "file",
        ]);

        if (Evidence::where(['id' => $evidence_id])->exists()) {
            $file = $request->file('file');
            $id_user = auth()->user();
            $evidence = Evidence::find($evidence_id);
            
            if ($evidence->file !== $file->getClientOriginalName()) {
                return response([
                    "status" => 0,
                    "message" => "La evidencia a actualizar no tiene el mismo nombre de archivo",
                ], 404);
            }

            $standardId = $evidence->standard_id;
            $typeEvidenceId = $evidence->type_evidence_id;
            $pathEvidence = $evidence->path;
            $pathEvidence = preg_replace('/^\/+/', '', $pathEvidence);
            $path = $file->storeAs('evidencias/'. $year . '/' . $semester . '/' .'estandar_' . $standardId . '/tipo_evidencia_'. $typeEvidenceId . '/' . $pathEvidence, $file->getClientOriginalName());
            $evidence->type = $file->getClientOriginalExtension();
            $evidence->size = $file->getSize();
            $evidence->save();
            return response([
                "status" => 1,
                "message" => "Evidencia actualizada exitosamente",
            ], 404);
        } else {
            return response([
                "status" => 0,
                "message" => "No se encontro la evidencia",
            ], 404);
        }
    }

    public function rename(Request $request, $year, $semester, $evidence_id) 
    {
        $request->validate([
            "new_filename" => "required|string",
        ]);
        if (Evidence::where("id", $evidence_id)->exists()) {
            $newFilename = $request->new_filename;
            $evidence = Evidence::find($evidence_id);
            $standardId = $evidence->standard_id;
            $typeEvidenceId = $evidence->evidence_type_id;
            $pathEvidence = $evidence->path;
            $currentPath = 'evidencias/' . $year . '/' . $semester . '/' .'estandar_' . $standardId . '/tipo_evidencia_'. $typeEvidenceId;
            $currentFilePath = $currentPath . $pathEvidence;
            $folder = Folder::find($evidence->folder_id);
            $folderName = $folder->path;
            $newFilePath = $currentPath . $folderName . '/' . $newFilename . '.' . $evidence->type;
            Storage::move($currentFilePath, $newFilePath);
            $evidence->name = $newFilename;
            $evidence->path = $folderName . '/' . $newFilename . '.' . $evidence->type;
            $evidence->file = $newFilename . '.'. $evidence->type;
            $evidence->save();
            return response([
                "status" => 1,
                "message" => "Nombre de evidencia actualizada exitosamente",
            ], 404);
        } else {
            return response([
                "status" => 0,
                "msg" => "No se encontro la evidencia",
            ], 404);
        }
    }

    public function delete($year, $semester, $evidence_id)
    {
        $id_user = auth()->user();
        if (Evidence::where("id", $evidence_id)->exists()) {
            $evidence = Evidence::find($evidence_id);
            $pathEvidence = 'evidencias/'. $year . '/' . $semester . '/estandar_' . $evidence->standard_id . '/tipo_evidencia_' . $evidence->evidence_type_id . $evidence->path;
            Storage::delete($pathEvidence);
            $evidence->delete();
            return response([
                "status" => 0,
                "message" => "Evidencia eliminada exitosamente",
            ], 404);
        } else {
            return response([
                "status" => 0,
                "message" => "No se encontro la evidencia",
            ], 404);
        }
    }

    public function download($year, $semester, $evidence_id)
    {
        if (Evidence::where("id", $evidence_id)->exists()) {
            $evidence = Evidence::find($evidence_id);
            $path = storage_path('app/' . 'evidencias/'. $year . '/' . $semester . '/estandar_' . $evidence->standard_id . '/tipo_evidencia_' . $evidence->evidence_type_id . $evidence->path);
            return response()->download($path);
        } else {
            return response([
                "status" => 0,
                "msg" => "No se encontro la evidencia",
            ], 404);
        }
    }

    public function view($year, $semester, $evidence_id)
    {
        if (Evidence::where("id", $evidence_id)->exists()) {
            $evidence = Evidence::find($evidence_id);
            $dateId = DateModel::dateId($year, $semester);
            $path = storage_path('app/' . 'evidencias/'. $year . '/' . $semester . '/estandar_' . $evidence->standard_id . '/tipo_evidencia_' . $evidence->evidence_type_id . $evidence->path);
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $contentType = $this->getContentType($extension);
            $fileContents = file_get_contents($path);
            $base64Content = base64_encode($fileContents);
            return response([
                "status" => 0,
                "evidence_id" => $evidence_id,
                "base64_content" => $base64Content,
            ], 200)->header('Content-Type', 'application/json');
        } else {
            return response([
                "status" => 0,
                "msg" => "No se encontró la evidencia",
            ], 404);
        }
    }
    
    private function getContentType($extension)
    {
        $contentTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
        ];
        if (isset($contentTypes[$extension])) {
            return $contentTypes[$extension];
        }
        return 'application/octet-stream';
    }
}