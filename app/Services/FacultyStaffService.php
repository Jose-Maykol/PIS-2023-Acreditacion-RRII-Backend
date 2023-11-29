<?php

namespace App\Services;

use App\Repositories\DateSemesterRepository;
use App\Repositories\FacultyStaffRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class FacultyStaffService
{

    protected $userRepository;
    protected $dateSemesterRepository;
    protected $facultyStaffRepository;
    public function __construct(FacultyStaffRepository $facultyStaffRepository, UserRepository $userRepository, DateSemesterRepository $dateSemesterRepository)
    {
        $this->facultyStaffRepository = $facultyStaffRepository;
        $this->dateSemesterRepository = $dateSemesterRepository;
        $this->userRepository = $userRepository;
    }
    public function createFacultyStaff($year, $semester, $data){
        $userAuth = auth()->user();
        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        
        if (!$this->dateSemesterRepository->dateSemesterExists2($year, $semester)) {
            throw new \App\Exceptions\DateSemester\DateSemesterNotFoundException();
        }
        $id_date_semester = $this->dateSemesterRepository->dateId($year, $semester);
        $data['date_id'] = $id_date_semester;
        $facultyStaff = $this->facultyStaffRepository->createFacultyStaff($data);
        return $facultyStaff;
    }
    public function updateFacultyStaff($year, $semester, $data){
        $userAuth = auth()->user();
        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        
        if (!$this->dateSemesterRepository->dateSemesterExists2($year, $semester)) {
            throw new \App\Exceptions\DateSemester\DateSemesterNotFoundException();
        }
        $date_id = $this->dateSemesterRepository->dateId($year, $semester);
        $facultyStaff = $this->facultyStaffRepository->updateFacultyStaff($date_id, $data);
        return $facultyStaff;
    }
    public function getFacultyStaff($year, $semester){
        $userAuth = auth()->user();
        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        
        if (!$this->dateSemesterRepository->dateSemesterExists2($year, $semester)) {
            throw new \App\Exceptions\DateSemester\DateSemesterNotFoundException();
        }
        $id_date_semester = $this->dateSemesterRepository->dateId($year, $semester);
        $facultyStaff = $this->facultyStaffRepository->getFacultyStaff($id_date_semester);
        return $facultyStaff;
    }
}