<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evidencias;
use App\Models\plan;
use App\Models\User;
use App\Models\Estandar;
use App\Models\Folder;
use App\Models\Evidence;
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
            "id_estandar" => "required|integer",
            "id_tipoEvidencia" => "required|integer",
            "files" => "required|array",
            "files.*" => "file",
            "path" => "nullable|string",
        ]);

        $userId = auth()->user()->id;

        $estandarId = $request->id_estandar;
        $tipoEvidenciaId = $request->id_tipoEvidencia;
        $generalPath = $request->has('path') ? $request->path : null;
        $parentFolder = null;
        
        $folder = Folder::where('path', $generalPath)->where('standard_id', $estandarId)->where('evidenceType_id', $tipoEvidenciaId)->first();
        if (!$folder) {
            $folder = new Folder([
                'name' => $generalPath == '' ? 'root' : $generalPath,
                'user_id' => $userId,
                'path' => $generalPath,
                'standard_id' => $estandarId,
                'evidenceType_id' => $tipoEvidenciaId,
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

                    $extractedPath = storage_path('app/evidencias/'. 'estandar_' . $estandarId . '/tipo_evidencia_'. $tipoEvidenciaId) . '/' . $generalPath;
                    for ($i = 0; $i < $zip->numFiles; $i++) 
                    {
                        $fileInfo = $zip->statIndex($i);
                        $fileName = $generalPath == null ? '/' . trim($fileInfo['name'], '/') : $generalPath . '/' . trim($fileInfo['name'], '/');
                        $isDirectory = substr($fileName, -1) === '/';
                        if ($isDirectory) {
                            if (Folder::where('path', $fileName)->where('standard_id', $estandarId)->where('evidenceType_id', $tipoEvidenciaId)->exists()) {
                                return response([
                                    "status" => 0,
                                    "message" => "Ya existe existe este carpeta",
                                    "file" => $fileName,
                                ], 404);
                            }
                        } else {
                           if (Evidence::where('path', $fileName)->where('standard_id', $estandarId)->where('evidenceType_id', $tipoEvidenciaId)->exists()) {
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
                    $this->createEvidencesAndFolders($extractedPath, $userId, $estandarId, $tipoEvidenciaId, $parentFolder);

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

                if (Evidence::where('path', $relativePath)->where('standard_id', $estandarId)->where('evidenceType_id', $tipoEvidenciaId)->exists()) {
                    return response([
                        "status" => 0,
                        "message" => "Ya existe existe este archivo",
                        "file" => $file->getClientOriginalName(),
                    ], 404);
                }

                $path = $file->storeAs('evidencias/'. 'estandar_' . $estandarId . '/tipo_evidencia_'. $tipoEvidenciaId . '/' . $generalPath, $file->getClientOriginalName());

                $evidence = new Evidence([
                    'name' => $file->getClientOriginalName(),
                    'file' => $file->getClientOriginalName(),
                    'type' => $file->getClientOriginalExtension(),
                    'size' => $file->getSize(),
                    'user_id' => $userId,
                    'standard_id' => $estandarId,
                    'evidenceType_id' => $tipoEvidenciaId,
                    'path' => $relativePath,
                    'folder_id' => $folder->id,
                ]);
                $evidence->save();
                return response([
                    "status" => 1,
                    "message" => "Evidencia creada exitosamente",
                ]);
            }
        }
    }

    private function createEvidencesAndFolders($path, $userId, $estandarId, $tipoEvidenciaId, $parentFolder = null)
    {   
    
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $path . '/' . $file;
                $relativePath = str_replace(storage_path('app/'), '', $filePath);
                $basePath = 'evidencias/'. 'estandar_' . $estandarId . '/tipo_evidencia_'. $tipoEvidenciaId . '/';
                $relativePath = str_replace($basePath, '', $relativePath);
                if (is_dir($filePath)) {
                    $folder = new Folder([
                        'name' => $file,
                        'user_id' => $userId,
                        'path' => $relativePath,
                        'standard_id' => $estandarId,
                        'evidenceType_id' => $tipoEvidenciaId,
                    ]);
                    if ($parentFolder) {
                        $folder->parent()->associate($parentFolder);
                    }
                    if ($parentFolder == null) {
                        $rootFolder = Folder::where('path', null)->where('standard_id', $estandarId)->where('evidenceType_id', $tipoEvidenciaId)->first();
                        if ($rootFolder) {
                            $folder->parent_id = $rootFolder->id;
                        }
                    }
                    $folder->save();
                    $this->createEvidencesAndFolders($filePath, $userId, $estandarId, $tipoEvidenciaId, $folder);
                } else {
                    if (Evidence::where('path', $relativePath)->where('standard_id', $estandarId)->where('evidenceType_id', $tipoEvidenciaId)->exists()) {
                        continue;
                    }
                    $fileInfo = pathinfo($filePath);
                    $evidence = new Evidence([
                        'name' => $fileInfo['filename'],
                        'file' => $fileInfo['basename'],
                        'type' => $fileInfo['extension'],
                        'size' => filesize($filePath),
                        'user_id' => $userId,
                        'standard_id' => $estandarId,
                        'evidenceType_id' => $tipoEvidenciaId,
                        'path' => $relativePath,
                    ]);
                    if ($parentFolder) {
                        $evidence->folder()->associate($parentFolder);
                    }
                    $evidence->save();
                }
            }
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            "id" => "required|integer",
            "id_tipo" => "required|integer", //Tipo de evidencia
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
                $evidencia->id_tipo = $request->id_tipo;
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
        if (Evidence::where("id", $id)->exists()) {
            $evidence = Evidence::find($id);
            $path = storage_path('app/' . 'evidencias/estandar_' . $evidence->standard_id . '/tipo_evidencia_' . $evidence->evidenceType_id . $evidence->path);
            return response()->download($path);
        } else {
            return response([
                "status" => 0,
                "msg" => "No se encontro la evidencia",
            ], 404);
        }
    }

    public function view($id)
    {
        if (Evidence::where("id", $id)->exists()) {
            $evidence = Evidence::find($id);
            $path = storage_path('app/' . 'evidencias/estandar_' . $evidence->standard_id . '/tipo_evidencia_' . $evidence->evidenceType_id . $evidence->path);
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $contentType = $this->getContentType($extension);
            $fileContents = file_get_contents($path);
            return response($fileContents)->header('Content-Type', $contentType);
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