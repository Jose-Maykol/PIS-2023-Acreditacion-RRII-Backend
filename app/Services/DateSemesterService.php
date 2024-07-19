<?php

namespace App\Services;

use App\Models\DateModel;
use App\Repositories\DateSemesterRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class DateSemesterService
{

    protected $userRepository;
    protected $dateSemesterRepository;

    public function __construct(UserRepository $userRepository, DateSemesterRepository $dateSemesterRepository)
    {
        $this->dateSemesterRepository = $dateSemesterRepository;
        $this->userRepository = $userRepository;
    }
    public function createDateSemester($year, $semester)
    {
        $userAuth = auth()->user();
        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        if ($this->dateSemesterRepository->checkIfDateSemesterExists($year, $semester)) {
            throw new \App\Exceptions\DateSemester\DateSemesterAlreadyExistsException();
        }
        $date_semester = $this->dateSemesterRepository->createDateSemester($year, $semester);
        return $date_semester;
    }

    public function statusDateSemester($year, $semester)
    {
        $userAuth = auth()->user();
        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        if (!$this->dateSemesterRepository->checkIfDateSemesterExists($year, $semester)) {
            throw new \App\Exceptions\DateSemester\DateSemesterNotFoundException();
        }
        $result = $this->dateSemesterRepository->statusDateSemester($year, $semester);
        return $result;
    }

    public function updateDateSemester($id_data_semester, $year, $semester)
    {
        $userAuth = auth()->user();
        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        if (!$this->dateSemesterRepository->dateSemesterExists($id_data_semester)) {
            throw new \App\Exceptions\DateSemester\DateSemesterNotFoundException();
        }
        if ($this->dateSemesterRepository->checkIfDateSemesterExists($year, $semester)) {
            throw new \App\Exceptions\DateSemester\DateSemesterAlreadyExistsException();
        }
        $date_semester = $this->dateSemesterRepository->updateDateSemester($id_data_semester, $year, $semester);
        return $date_semester;
    }

    public function listDateSemester(){

        $date_semesters = $this->dateSemesterRepository->listDateSemester()->values()->toArray();
        return $date_semesters;
    }

    public function infoDateSemester($year, $semester){
        $id_date_semester = $this->dateSemesterRepository->dateId($year, $semester);
        // $date_semester = $this->dateSemesterRepository->readDateSemester($id_date_semester);
        $date_semester = $this->dateSemesterRepository->checkClosingDate($id_date_semester); 
        return $date_semester;
    }
    public function closeDateSemester($year, $semester, $closing_date){
        $id_date_semester = $this->dateSemesterRepository->dateId($year, $semester);
        $date_semester = $this->dateSemesterRepository->closeDateSemester($id_date_semester, $closing_date);
        $date_semester = $this->dateSemesterRepository->checkClosingDate($id_date_semester);
        return $date_semester;
    }
    
}
