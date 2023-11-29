<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\DateModel;
use App\Models\Evidence;

class FoldersController extends Controller
{
    public function create(Request $request)
    { 
        $request->validate([
            'name' => 'required',
            'standard_id' => 'required|integer',
            'evidence_type_id' => 'required|integer',
            'path' => 'required|string',
        ]);

        $userId = auth()->user()->id;
        $year = $request->route('year');
        $semester = $request ->route('semester');
        $dateId = DateModel::dateId($year, $semester);
        $standard = $request->standard_id;
        $evidenceType = $request->evidence_type_id;
        $folderName = $request->name;
        $generalPath = $request->path;

        $parentFolder = Folder::where('path', $generalPath)->where('standard_id', $standard)->where('evidence_type_id', $evidenceType)->first();

        if (!$parentFolder) {
            return response()->json([
                'status' => 0,
                'message' => 'No existe la carpeta padre',
            ]);
        }

        $parentFolderId = $parentFolder->id;

        $folder = Folder::where('path', $generalPath . '/' . $folderName)->where('standard_id', $standard)->where('evidence_type_id', $evidenceType)->first();

        if ($folder) {
            return response()->json([
                'status' => 0,
                'message' => 'Ya existe una carpeta con el mismo nombre',
            ]);
        }

        $relativeFolderPath = $generalPath == '/' ? ($generalPath . $folderName) : ( $generalPath . '/' . $folderName);
        $pathNewFolder = storage_path('app/evidencias/'. $year . '/' . $semester . '/estandar_' . $standard . '/tipo_evidencia_' . $evidenceType . $relativeFolderPath); 

        if (!File::exists($pathNewFolder)) {

            File::makeDirectory($pathNewFolder, 0777, true);
            $folder = new Folder([
                'name' => $request->name,
                'standard_id' => $request->standard_id,
                'evidence_type_id' => $request->evidence_type_id,
                'path' => $generalPath == '/' ? ($generalPath . $folderName) : ( $generalPath . '/' . $folderName),
                'user_id' => $userId,
                'date_id' => $dateId,
                'parent_id' => $parentFolderId,
            ]);

            $folder->save();
  
            return response()->json([
                'status' => 1,
                'message' => 'Carpeta creada correctamente',
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Ya existe una carpeta con el mismo nombre',
            ]);
        }
    }

    public function rename(Request $request,  $year, $semester, $folder_id)
    {
        $request->validate([
            "new_name" => "required|string",
        ]);
        if (Folder::where("id", $folder_id)) {
            $newFoldername = $request->new_name;
            $folder = Folder::find($folder_id);
            $standardId = $folder->standard_id;
            $typeEvidenceId = $folder->evidence_type_id;
            $folderName = $folder->name;
            $lastOcurrence = strrpos($folder->path, $folderName);
            $pathFolder = substr_replace($folder->path, '', $lastOcurrence, strlen($folderName));
            $currentPath = 'evidencias/' . $year . '/' . $semester . '/' .'estandar_' . $standardId . '/tipo_evidencia_'. $typeEvidenceId;
            $currentFolderPath = $currentPath . $folder->path;
            $newFolderPath = $currentPath . $pathFolder . $newFoldername;
            Storage::move($currentFolderPath, $newFolderPath);
            $folder->name = $newFoldername;
            $folder->path = $pathFolder . $newFoldername;
            $folder->save();
            // actualizar evidencias con nuevo path
            $evidences = Evidence::where('folder_id', $folder_id)->get();
            if ($evidences) {
                foreach ($evidences as $evidence) {
                    $evidence->path = $pathFolder . $newFoldername . '/' . $evidence->file;
                    $evidence->save();
                }
            }
            return response([
                "status" => 1,
                "message" => "Nombre de carpeta actualizado exitosamente",
            ], 200);
        } else {
            return response([
                "status" => 0,
                "message" => "No se encontro la carpeta",
            ], 404);
        }
    }

    public function delete($year, $semester, $folder_id)
    {
        $folder = Folder::find($folder_id);

        if (!$folder) {
            return response([
                "status" => 0,
                "message" => "No se encontro la carpeta",
            ], 404);
        }

        $standardId = $folder->standard_id;
        $typeEvidenceId = $folder->evidence_type_id;
        $currentPath = 'evidencias/' . $year . '/' . $semester . '/' .'estandar_' . $standardId . '/tipo_evidencia_'. $typeEvidenceId;
        $currentFolderPath = $currentPath . $folder->path;
        // Obtiene las evidencias vinculadas y las borra
        $evidences = Evidence::where('folder_id', $folder_id)->get();

        if ($evidences) {
            foreach ($evidences as $evidence) {
                $evidence->delete();
            }
        }
        Storage::deleteDirectory($currentFolderPath);
        $folder->delete();
        return response([
            "status" => 1,
            "message" => "Carpeta eliminada exitosamente",
        ], 200);
    }

    public function move(Request $request, $year, $semester, $folder_id)
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

        if ($parentFolder->id == $folder_id) {
            return response([
                "status" => 0,
                "message" => "No se puede mover la carpeta dentro de si misma",
            ], 404);
        }

        $newPath = $parentFolder->path;
        $folder = Folder::find($folder_id);

        if ($parentFolder->evidence_type_id != $folder->evidence_type_id) {
            return response([
                "status" => 0,
                "message" => "No se puede mover la carpeta a una carpeta de otro tipo de evidencia",
            ], 404);
        
        }

        if ($parentFolder->standard_id != $folder->standard_id) {
            return response([
                "status" => 0,
                "message" => "No se puede mover la carpeta a una carpeta de otro estándar",
            ], 404);
        }

        $standardId = $folder->standard_id;
        $typeEvidenceId = $folder->evidence_type_id;
        $folderName = $folder->name;
        $currentPath = 'evidencias/' . $year . '/' . $semester . '/' .'estandar_' . $standardId . '/tipo_evidencia_'. $typeEvidenceId;
        $currentFolderPath = $currentPath . $folder->path;
        $newFolderPath = $currentPath . $newPath . '/' . $folderName;
        Storage::move($currentFolderPath, $newFolderPath);
        $folder->path = $newPath . '/' . $folderName;
        $folder->parent_id = $parentId;
        $folder->save();
        // actualizar evidencias con nuevo path
        $evidences = Evidence::where('folder_id', $folder_id)->get();
        if ($evidences) {
            foreach ($evidences as $evidence) {
                $evidence->path = $newPath . '/' . $folderName . '/' . $evidence->file;
                $evidence->save();
            }
        }
        return response([
            "status" => 1,
            "message" => "Carpeta movida exitosamente",
        ], 200);
    }

    public function list(Request $request, $year, $semester)
    {
        $request->validate([
            'folder_id' => 'integer|nullable',
            'standard_id' => 'required|integer',
            'evidence_type_id' => 'required|integer',
        ]);

        $standardId = $request->standard_id;
        $evidenceTypeId = $request->evidence_type_id;
        $folderId = $request->folder_id;
        $dateId = DateModel::dateId($year, $semester);

        if ($folderId == null) {
            $parentFolder = Folder::where('parent_id', null)
                ->where('standard_id', $standardId)
                ->where('evidence_type_id', $evidenceTypeId)
                ->where('date_id', $dateId)
                ->first();
            $parentFolderId = $parentFolder->id;
            $folders = Folder::where('parent_id', $parentFolderId )->get();
            if (!$folders) {
                return response([
                    "status" => 0,
                    "message" => "No se encontraron carpetas",
                ], 404);
            }
            return response([
                "status" => 1,
                "data" => $folders,
            ], 200);
        }

        
        $folder = Folder::find($folderId);

        if (!$folder) {
            return response([
                "status" => 0,
                "message" => "No se existe la carpeta",
            ], 404);
        }

        $folders = Folder::where('parent_id', $folderId)->get();

        if ($folders->isEmpty()) {
            return response([
                "status" => 0,
                "message" => "No hay carpetas dentro de esta carpeta",
            ], 404);
        }
        return response([
            "status" => 1,
            "data" => $folders,
        ], 200);
    }
}
