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

    
}
