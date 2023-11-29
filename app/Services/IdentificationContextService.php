<?php

namespace App\Services;

use App\Models\IdentificationContextModel;
use App\Repositories\DateSemesterRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Repositories\IdentificationContextRepository;

class IdentificationContextService
{

    protected $userRepository;
    protected $dateSemesterRepository;
    protected $identContRepository;
    public function __construct(IdentificationContextRepository $identContRepository, UserRepository $userRepository, DateSemesterRepository $dateSemesterRepository)
    {
        $this->identContRepository = $identContRepository;
        $this->dateSemesterRepository = $dateSemesterRepository;
        $this->userRepository = $userRepository;
    }

    public function createIdentificationContext($year, $semester, $data){
        $userAuth = auth()->user();
        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        
        if (!$this->dateSemesterRepository->dateSemesterExists2($year, $semester)) {
            throw new \App\Exceptions\DateSemester\DateSemesterNotFoundException();
        }
        $id_date_semester = $this->dateSemesterRepository->dateId($year, $semester);
        $data['date_id'] = $id_date_semester;
        #\Illuminate\Support\Facades\Log::info($data);
        $ident_cont = $this->identContRepository->createIdentificationContext($data);
        return $ident_cont;
    }
    public function updateIdentificationContext($year, $semester, $data){
        $userAuth = auth()->user();
        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        
        if (!$this->dateSemesterRepository->dateSemesterExists2($year, $semester)) {
            throw new \App\Exceptions\DateSemester\DateSemesterNotFoundException();
        }
        $date_id = $this->dateSemesterRepository->dateId($year, $semester);
        $ident_cont = $this->identContRepository->updateIdentificationContext($date_id, $data);
        return $ident_cont;
    }
    public function getIdentificationContext($year, $semester){
        $userAuth = auth()->user();
        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        
        if (!$this->dateSemesterRepository->dateSemesterExists2($year, $semester)) {
            throw new \App\Exceptions\DateSemester\DateSemesterNotFoundException();
        }
        $id_date_semester = $this->dateSemesterRepository->dateId($year, $semester);
        $ident_cont = $this->identContRepository->getIdentificationContext($id_date_semester);
        return $ident_cont;
    }
}