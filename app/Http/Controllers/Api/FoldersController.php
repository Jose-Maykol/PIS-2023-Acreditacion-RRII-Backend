<?php

namespace App\Http\Controllers\Api;

class FoldersController extends Controller
{
    public function create()
    { 
        $request->validate([
            'name' => 'required',
            'standard_id' => 'required',
            'evidenceType_id' => 'required',
            'path' => 'nullable|required',
        ]);

        $userId = auth()->user()->id;
        $standard = $request->standard_id;
        $evidenceType = $request->evidenceType_id;
        $folderName = $request->name;
        $generalPath = $request->has('path') ? $request->path : null;
        
        $folder = Folder::where('path', $generalPath)->where('standard_id', $standard)->where('evidenceType_id', $evidenceType)->first();

        if ($folder) {
            return response()->json([
                'status' => 0,
                'message' => 'Ya existe una carpeta con el mismo nombre',
            ]);
        }

        $pathNewFolder = storage_path() . 'app/evidencias/estandar_' . $standard . '/tipo_evidencia_' . $evidenceType . $generalPath . '/' . $folderName;

        if (!File::exists($pathNewFolder)) {

            File::makeDirectory($pathNewFolder, 0777, true);
            $folder = new Folder([
                'name' => $request->name,
                'standard_id' => $request->standard_id,
                'evidenceType_id' => $request->evidenceType_id,
                'path' => $generalPath,
                'user_id' => $userId,
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