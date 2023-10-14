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

    public function listStandards(Request $request)
    {
        $userAuth = auth()->user();

        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException("El usuario no está autorizado", 403);
        }

        if ($this->userRepository->checkIfEmailExists($request->email)) {
            throw new \App\Exceptions\User\EmailAlreadyExistsException("Correo ya existente", 422);
        }

        if (!$this->roleRepository->checkIfRoleExists($request->role)){
            throw new \App\Exceptions\User\RoleNotFoundException("El rol no existe", 404);
        }

        return $this->userRepository->createUser($request->email, $request->role);

        
    }
}