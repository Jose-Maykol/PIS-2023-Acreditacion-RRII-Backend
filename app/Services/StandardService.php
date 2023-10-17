<?php

namespace App\Services;

use App\Repositories\StandardRepository;
use App\Repositories\UserRepository;

use Illuminate\Http\Request;

class StandardService
{

    protected $standardRepository;
    protected $userRepository;

    public function __construct(StandardRepository $standardRepository, UserRepository $userRepository)
    {

        $this->standardRepository = $standardRepository;
        $this->userRepository = $userRepository;
    }

    public function listStandardsAssignment($year, $semester)
    {
        $userAuth = auth()->user();

        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }

        return $this->standardRepository->listStandardsAssignment($year, $semester);
    }

    public function listPartialStandards($year, $semester)
    {
        return $this->standardRepository->listPartialStandards($year, $semester);
    }

    public function changeStandardAssignment($standard_id, Request $request)
    {
        $userAuth = auth()->user();

        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        if (!$this->standardRepository->getStandardActiveById($standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }
        return $this->standardRepository->changeStandardAssignment($standard_id, $request->users);
    }

    public function showStandard($standard_id)
    {
        $userAuth = auth()->user();

        if (!$this->standardRepository->getStandardActiveById($standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }

        $standard = $this->standardRepository->getFullStandard($standard_id);
        $standard->standardStatus = $this->standardRepository->getAllStandardStatus();
        $standard->isManager = $this->userRepository->checkIfUserIsManagerStandard($standard_id, $userAuth);
        $standard->isAdministrator = $this->userRepository->isAdministrator($userAuth);

        return $standard;
    }

    public function updateStandardHeader($standard_id, Request $request)
    {
        $userAuth = auth()->user();

        if (!$this->standardRepository->getStandardActiveById($standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }

        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }

        return $this->standardRepository->updateStandardHeader($standard_id, $request->description, $request->factor, $request->dimension, $request->related_standards);
    }

    public function updateStandardStatus($standard_id, $standard_status_id)
    {

        $userAuth = auth()->user();

        if (!$this->standardRepository->getStandardActiveById($standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }

        if (!$this->standardRepository->getStandardStatusActiveById($standard_status_id)) {
            throw new \App\Exceptions\Standard\StandardStatusNotFoundException();
        }

        if (!$this->userRepository->checkIfUserIsManagerStandard($standard_id, $userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        
        return $this->standardRepository->updateStandardStatus($standard_id, $standard_status_id);
    }
}
