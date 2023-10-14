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

}