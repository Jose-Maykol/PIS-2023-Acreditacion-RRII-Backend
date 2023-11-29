<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DateSemesterRequest;
use App\Http\Requests\IdentificationContextRequest;
use Illuminate\Http\Request;
use App\Models\DateModel;
use App\Services\DateSemesterService;
use App\Services\IdentificationContextService;
use Exception;

class IdentificationContextController extends Controller
{
    protected $dateSemesterService;
    protected $identContextService;
    public function __construct(DateSemesterService $dateSemesterService, IdentificationContextService $identContextService)
    {
        $this->identContextService = $identContextService;
        $this->dateSemesterService = $dateSemesterService;
    }
    public function createIdentificationContext($year, $semester, IdentificationContextRequest $request){
        try {
            $request->validated();
            $result = $this->identContextService->createIdentificationContext($year, $semester, $request->all());
            return response([
                "status" => 1,
                "message" => "Identificación y contexto creado exitosamente",
                "data" => $result
            ], 201);
        } 
        catch (\App\Exceptions\User\UserNotAuthorizedException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
        catch (\App\Exceptions\DateSemester\DateSemesterNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }
    public function updateIdentificationContext($year, $semester, IdentificationContextRequest $request){
        try {
            $request->validated();
            $result = $this->identContextService->updateIdentificationContext($year, $semester, $request->all());
            return response([
                "status" => 1,
                "message" => "Identificación y contexto actualizado exitosamente",
                "data" => $result
            ], 200);
        } 
        catch (\App\Exceptions\User\UserNotAuthorizedException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
        catch (\App\Exceptions\DateSemester\DateSemesterNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }
    public function getIdentificationContext($year, $semester, IdentificationContextRequest $request){
        try {
            $result = $this->identContextService->getIdentificationContext($year, $semester);
            return response([
                "status" => 1,
                "message" => "Identificación y contexto del periodo $year - $semester",
                "data" => $result
            ], 200);
        } 
        catch (\App\Exceptions\User\UserNotAuthorizedException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
        catch (\App\Exceptions\DateSemester\DateSemesterNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

}