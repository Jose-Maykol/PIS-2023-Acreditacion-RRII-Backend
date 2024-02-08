<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FacultyStaffRequest;
use App\Services\DateSemesterService;
use App\Services\FacultyStaffService;
use Illuminate\Http\Request;

use Exception;

class FacultyStaffController extends Controller
{
    protected $dateSemesterService;
    protected $facultyStaffService;
    public function __construct(DateSemesterService $dateSemesterService, FacultyStaffService $facultyStaffService)
    {
        $this->facultyStaffService = $facultyStaffService;
        $this->dateSemesterService = $dateSemesterService;
    }
    public function createFacultyStaff($year, $semester, FacultyStaffRequest $request){
        try {
            $request->validated();
            $result = $this->facultyStaffService->createFacultyStaff($year, $semester, $request->all());
            return response([
                "status" => 1,
                "message" => "Datos de personal docente creado exitosamente",
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
    public function updateFacultyStaff($year, $semester, FacultyStaffRequest $request){
        try {
            $request->validated();
            $result = $this->facultyStaffService->updateFacultyStaff($year, $semester, $request->all());
            return response([
                "status" => 1,
                "message" => "Datos de personal docente actualizado exitosamente",
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
    public function getFacultyStaff($year, $semester, FacultyStaffRequest $request){
        try {
            $result = $this->facultyStaffService->getFacultyStaff($year, $semester);
            return response([
                "status" => 1,
                "message" => "Datos de personal docente del periodo $year - $semester",
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

    public function reportAnual(Request $request)
    {
        $result = $this->facultyStaffService->reportAnual($request);
        return $result;
    }
}