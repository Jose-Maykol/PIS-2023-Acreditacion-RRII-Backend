<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evidencias;
use App\Models\plan;
use App\Models\StandardModel;
use App\Models\Folder;
use App\Models\Evidence;
use App\Models\EvidenciasTipo;
use App\Models\DateModel;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Http\Request;
use ZipArchive;



class EvidenciasController extends Controller
{
    /* Deprecated */
    public function create(Request $request)
    {
        $request->validate([
            "standard_id" => "required|integer",
            "type_evidence_id" => "required|integer",
            "plan_id" => "integer",
            "file" => "required|array",
            "file.*" => "file",
            "path" => "string",
        ]);

        $userId = auth()->user()->id;
        $year = $request->route('year');
        $semester = $request ->route('semester');
        $dateId = DateModel::dateId($year, $semester);
        $standardId = $request->standard_id;
        $typeEvidenceId = $request->type_evidence_id;
        $planId = $request->has('plan_id')? $request->plan_id : null;
        $generalPath = $request->has('path') ? $request->path : '/';
        $parentFolder = null;
        
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
                "msg" => "Evidencia",
                "data" => $evidencia,
            ]);
        } else {
            return response([
                "status" => 0,
                "msg" => "No se encontro el evidencia",
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
            "path" => "string",
        ]);

        $userId = auth()->user()->id;
        $year = $request->route('year');
        $semester = $request ->route('semester');
        $dateId = DateModel::dateId($year, $semester);
        $standardId = $request->standard_id;
        $typeEvidenceId = $request->type_evidence_id;
        $planId = $request->has('plan_id')? $request->plan_id : null;
        $generalPath = $request->has('path') ? $request->path : '/';
        $parentFolder = null;

        $standardBelongsSemester = StandardModel::where('date_id', $dateId)->where('id', $standardId)->exists();

        if (!$standardBelongsSemester) {
            return response([
                "status" => 0,
                "message" => "No existe este estándar en el periodo " . $year . $semester,
            ], 404);
        }
        
        // Consulta si existe el folder padre, si no existe lo crea
        $folder = Folder::where('path', $generalPath)->where('standard_id', $standardId)->where('evidence_type_id', $typeEvidenceId)->first();

        if (!$folder && $generalPath == '/') {
            $folder = new Folder([
                'name' => $generalPath == '/' ? 'root' : $generalPath,
                'user_id' => $userId,
                'path' => $generalPath,
                'standard_id' => $standardId,
                'evidence_type_id' => $typeEvidenceId,
                'date_id' => $dateId,
            ]);
            $folder->save();
        } else if ($folder) {
            $parentFolder = $folder->id;
        } else {
            return response([
                "status" => 0,
                "message" => "No existe el path",
            ], 404);
        }

        foreach ($request->file('files') as $file) {
            if ($file->getClientOriginalExtension() == 'zip') 
            {
                $zip = new ZipArchive;
                if ($zip->open($file) === TRUE) 
                {
                    $extractedPath = storage_path('app/evidencias/'. $year . '/' . $semester . '/' . 'estandar_' . $standardId . '/tipo_evidencia_'. $typeEvidenceId) . $generalPath;
                    for ($i = 0; $i < $zip->numFiles; $i++) 
                    {
                        $fileInfo = $zip->statIndex($i);
                        $fileName = $generalPath == '/' ? $generalPath . trim($fileInfo['name'], '/') : $generalPath . '/' . trim($fileInfo['name'], '/');
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
                }
                else 
                {
                    return response([
                        "status" => 0,
                        "message" => "Error al descomprimir el archivo ZIP",
                    ], 404);
                }
            } else 
            {
                $relativePath = $generalPath == '/' ? $generalPath . $file->getClientOriginalName() : $generalPath . '/' . $file->getClientOriginalName();

                if (Evidence::where('path', $relativePath)->where('standard_id', $standardId)->where('evidence_type_id', $typeEvidenceId)->exists()) {
                    return response([
                        "status" => 0,
                        "message" => "Ya existe existe este archivo",
                        "file" => $file->getClientOriginalName(),
                    ], 404);
                }

                $path = $file->storeAs('evidencias/'. $year . '/' . $semester . '/' .'estandar_' . $standardId . '/tipo_evidencia_'. $typeEvidenceId . $generalPath, $file->getClientOriginalName());

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
            }
        }
        return response([
            "status" => 1,
            "message" => "Evidencia(s) creada exitosamente",
        ]);
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
            ], 200);
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
            if ($folder->path == '/') {
                $folderName = $folder->path;
            } else {
                $folderName = $folder->path . '/';
            }
            $newFilePath = $currentPath . $folderName . $newFilename . '.' . $evidence->type;
            Storage::move($currentFilePath, $newFilePath);
            $evidence->name = $newFilename;
            $evidence->path = $folderName . $newFilename . '.' . $evidence->type;
            $evidence->file = $newFilename . '.'. $evidence->type;
            $evidence->save();
            return response([
                "status" => 1,
                "message" => "Nombre de evidencia actualizada exitosamente",
            ], 200);
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
            ], 200);
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
            $dateId = $evidence->date_id;
            $date = DateModel::find($dateId);
            $path = storage_path('app/' . 'evidencias/'. $date->year . '/' . $date->semester . '/estandar_' . $evidence->standard_id . '/tipo_evidencia_' . $evidence->evidence_type_id . $evidence->path);
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $contentType = $this->getContentType($extension);
            $fileContents = file_get_contents($path);
            $base64Content = base64_encode($fileContents);
            return response([
                "status" => 1,
                "data" => [
                    "content" => $base64Content,
                    "name" => $evidence->name,
                    "extension" => $extension,
                    "type" => $contentType,
                ]
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

    public function move(Request $request, $year, $semester, $evidence_id)
    {
        $request->validate([
            "parent_id" => "required|integer",
        ]);

        $parentId = $request->parent_id;
        $parentFolder = Folder::find($parentId);

        if (!$parentFolder) {
            return response([
                "status" => 0,
                "message" => "No se encontro la carpeta",
            ], 404);
        }

        $evidence = Evidence::find($evidence_id);

        if (!$evidence) {
            return response([
                "status" => 0,
                "message" => "No se encontro la evidencia",
            ], 404);
        }

        if ($evidence->folder_id == $parentFolder->id) {
            return response([
                "status" => 0,
                "message" => "La evidencia ya se encuentra en esta carpeta",
            ], 404);
        }

        if ($evidence->evidence_type_id != $parentFolder->evidence_type_id) {
            return response([
                "status" => 0,
                "message" => "La evidencia no pertenece a este tipo de evidencia",
            ], 404);
        }

        if ($evidence->standard_id != $parentFolder->standard_id) {
            return response([
                "status" => 0,
                "message" => "La evidencia no pertenece a este estándar",
            ], 404);
        }

        $standardId = $evidence->standard_id;
        $typeEvidenceId = $evidence->evidence_type_id;
        $currentPath = 'evidencias/' . $year . '/' . $semester . '/' .'estandar_' . $standardId . '/tipo_evidencia_'. $typeEvidenceId;
        $currentEvidencePath = $currentPath . $evidence->path;
        $newEvidencePath = $currentPath . $parentFolder->path . '/' . $evidence->file;
        Storage::move($currentEvidencePath, $newEvidencePath);
        $evidence->path = $parentFolder->path . '/' . $evidence->file;
        $evidence->folder_id = $parentFolder->id;
        $evidence->save();
        return response([
            "status" => 1,
            "message" => "Evidencia movida exitosamente",
        ], 404);
    }

    public function reportAllEvidences(Request $request)
    {
        $tempfiledocx = tempnam(sys_get_temp_dir(), 'PHPWord');
        $template = new \PhpOffice\PhpWord\TemplateProcessor('plantilla-evidencias.docx');
        //Rango de periodos
        $startYear = $request->input('startYear');
        $startSemester = $request->input('startSemester');
        $endYear = $request->input('endYear');
        $endSemester = $request->input('endSemester');
        $dates = DateModel::where(function ($query) use ($startYear, $startSemester, $endYear, $endSemester) {
            $query->where(function ($query) use ($startYear, $startSemester) {
                $query->where('year', '>', $startYear)
                    ->orWhere(function ($query) use ($startYear, $startSemester) {
                        $query->where('year', $startYear)
                                ->where('semester', '>=', $startSemester);
                    });
            })
            ->where(function ($query) use ($endYear, $endSemester) {
                $query->where('year', '<', $endYear)
                    ->orWhere(function ($query) use ($endYear, $endSemester) {
                        $query->where('year', $endYear)
                                ->where('semester', '<=', $endSemester);
                    });
            });
        })->get();        
        $standards = StandardModel::where("date_id", 1)->orderBy('nro_standard')->get();
        if($standards->count() > 0){
            $template->cloneBlock('block_periodo', $dates->count(), true, true);
            $template->cloneBlock('block_estandar', $standards->count(), true, true);

            foreach ($standards as $key => $standard){
                // Dimensión
                $template->setValue('dimension#'. ($key + 1) , $standard->dimension);
                // Factor
                $template->setValue('factor#'. ($key + 1) , $standard->factor);
                // Estandar
                $template->setValue('n-e#'. ($key + 1) , $standard->nro_standard);
                $template->setValue('estandar#'. ($key + 1) , $standard->name);
                
                
                //Periodos
                foreach ($dates as $j => $date){
                    $template->setValue('year#' . ($j + 1) . '#'.($key + 1) , $date->year);
                    $template->setValue('semester#' . ($j + 1) . '#'.($key + 1) , $date->semester);
                    $evidencesCount = Evidence::where("standard_id", $standard->id)->where("date_id", $date->id)->count();
                    if($evidencesCount > 0){
                        $template->cloneRow('n#' . ($j+1) . '#'.($key + 1), $evidencesCount); 
                        $evidencias = Evidence::where("standard_id", $standard->id)->where("date_id", $date->id)->get();
                        foreach ($evidencias as $m => $evidence){
                            $template->setValue('n#' . ($j+1). "#". ($key + 1) . "#" . ($m+1), ($m+1));
                            $template->setValue('codigo#' . ($j+1). "#". ($key + 1) . "#" . ($m+1), "No data");
                            $template->setValue('nombre#' . ($j+1). "#". ($key + 1) . "#" . ($m+1), $evidence->name);
                            $template->setValue('tipo#' . ($j+1). "#". ($key + 1) . "#" . ($m+1), EvidenciasTipo::evidenceId($evidence->evidence_type_id));
                            $template->setValue('tamaño#' . ($j+1). "#". ($key + 1) . "#" . ($m+1), $evidence->size);
                            $template->setValue('fecha#' . ($j+1). "#". ($key + 1) . "#" . ($m+1), $evidence->created_at);
                        }
                    }else{
                        $template->cloneRow('n#' . ($j+1) . '#'.($key + 1), -1); 
                        $template->replaceBlock("block_tabla#" . ($j+1) . "#" . ($key + 1), "No hay evidencias");
                    }
                } 
            }

            $template->saveAs($tempfiledocx);
            $headers = [
                'Content-Type' => 'application/msword',
                'Content-Disposition' => 'attachment;filename="evidencias.docx"',
            ];
            return response()->download($tempfiledocx, 'reporte-evidencias.docx', $headers);
        } else{
            return response([
                "message" => "!No cuenta con ningún estándar todavía en este periodo",
            ], 404);
        }
    } 
}