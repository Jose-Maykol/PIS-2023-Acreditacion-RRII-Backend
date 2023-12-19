<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DateSemesterRequest;
use Illuminate\Http\Request;
use App\Models\DateModel;
use App\Services\DateSemesterService;
use Exception;

class DateSemestersController extends Controller
{
    protected $dateSemesterService;

    public function __construct(DateSemesterService $dateSemesterService)
    {
        $this->dateSemesterService = $dateSemesterService;
    }

    public function createDateSemester(DateSemesterRequest $request)
    {
        try {
            $request->validated();
            $result = $this->dateSemesterService->createDateSemester($request->year, $request->semester);
            return response()->json([
                "status" => 1,
                "message" => "Periodo creado exitosamente",
                "data" => $result
            ], 201);
        } catch (\App\Exceptions\User\UserNotAuthorizedException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (\App\Exceptions\DateSemester\DateSemesterAlreadyExistsException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    public function updateDateSemester(DateSemesterRequest $request)
    {
        try {
            $request->validated();
            $result = $this->dateSemesterService->updateDateSemester($request->id, $request->year, $request->semester);
            return response()->json([
                "status" => 1,
                "message" => "Periodo actualizado exitosamente",
                "data" => $result
            ], 200);
        } catch (\App\Exceptions\User\UserNotAuthorizedException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (\App\Exceptions\DateSemester\DateSemesterNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (\App\Exceptions\DateSemester\DateSemesterAlreadyExistsException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }
    public function listDateSemester(DateSemesterRequest $request){
        try {
            $result = $this->dateSemesterService->listDateSemester();
            return response()->json([
                "status" => 1,
                "message" => "Lista de periodos",
                "data" => $result
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => "Problema en el servidor",
            ], 500);
        }
    }
    public function infoDateSemester($year, $semester){
        try {
            $result = $this->dateSemesterService->infoDateSemester($year, $semester);
            return response()->json([
                "status" => 1,
                "message" => "Información del periodo $year - $semester",
                "data" => $result
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => "Problema en el servidor",
            ], 500);
        }
    }
    public function closeDateSemester($year, $semester, DateSemesterRequest $request){
        try {
            $request->validated();
            $result = $this->dateSemesterService->closeDateSemester($year, $semester, $request->closing_date);
            return response()->json([
                "status" => 1,
                "message" => "Periodo $year - $semester cerrado",
                "data" => $result
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => "Problema en el servidor",
            ], 500);
        }
    }
}
