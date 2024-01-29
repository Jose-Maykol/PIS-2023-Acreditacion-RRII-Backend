<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Http\Requests\FolderRequest;
use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\DateModel;
use App\Models\Evidence;
use App\Services\FolderService;
use Exception;

class FolderController extends Controller
{
    protected $folderService;

    public function __construct(FolderService $folderService)
    {

        $this->folderService = $folderService;
    }

    public function createFolder(FolderRequest $request)
    {
        try {
            $request->validated();
            $result = $this->folderService->createFolder($request);
            return response([
                "status" => 1,
                "message" => "Folder creado con existo",
                "data" => $result,
            ],201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
    public function renameFolder(FolderRequest $request)
    {
        try {
            $request->validated();
            $result = $this->folderService->renameFolder($request);
            return response([
                "status" => 1,
                "message" => "Nombre de carpeta actualizado exitosamente",
                "data" => $result
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function moveFolder(FolderRequest $request, $year, $semester, $folder_id)
    {
        try {
            $request->validated();
            $result = $this->folderService->moveFolder($request);
            return response([
                "status" => 1,
                "message" => "Carpeta movida exitosamente",
                "data" => $result
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], 404);
        }
    }


    public function deleteFolder($year, $semester, $folder_id)
    {
        try {
            $result = $this->folderService->deleteFolder($year, $semester, $folder_id);
            return response([
                "status" => 1,
                "message" => "Carpeta eliminada exitosamente",
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], 404);
        }        
    }

    
    public function listFolder(FolderRequest $request, $year, $semester)
    {
        try {
            $request->validated();
            $result = $this->folderService->listFolder($request);
            return response([
                "status" => 1,
                "message" => "Lista de carpetas",
                "data" => $result
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
