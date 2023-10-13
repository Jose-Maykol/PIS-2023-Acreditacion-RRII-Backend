<?php

namespace App\Services;

use App\Models\User;

use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;

use Illuminate\Http\Request;

class UserService
{

    protected $userRepository;
    protected $roleRepository;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {

        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    public function createUser(Request $request)
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
