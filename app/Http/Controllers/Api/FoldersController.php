<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\DateModel;
use App\Models\User;

class FoldersController extends Controller
{
    public function create(Request $request)
    { 
        $request->validate([
            'name' => 'required',
            'standard_id' => 'required|integer',
            'evidence_type_id' => 'required|integer',
            'path' => 'nullable',
        ]);

        $userId = auth()->user()->id;
        $year = $request->route('year');
        $semester = $request ->route('semester');
        $dateId = DateModel::dateId($year, $semester);
        $standard = $request->standard_id;
        $evidenceType = $request->evidence_type_id;
        $folderName = $request->name;
        $generalPath = $request->has('path') ? $request->path : null;

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

        $pathNewFolder = storage_path('app/evidencias/'. $year . '/' . $semester . '/estandar_' . $standard . '/tipo_evidencia_' . $evidenceType . $generalPath . '/' . $folderName); 

        if (!File::exists($pathNewFolder)) {

            File::makeDirectory($pathNewFolder, 0777, true);
            $folder = new Folder([
                'name' => $request->name,
                'standard_id' => $request->standard_id,
                'evidence_type_id' => $request->evidence_type_id,
                'path' => $generalPath . '/' . $folderName,
                'user_id' => $userId,
                'date_id' => $dateId,
                'parent_id' => $parentFolderId,
            ]);

            $folder->save();
  
            return response()->json([
                'status' => 1,
                'message' => 'Carpeta creada correctamente',
                'folder' => $folder,
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Ya existe una carpeta con el mismo nombre',
            ]);
        }
    }
}
