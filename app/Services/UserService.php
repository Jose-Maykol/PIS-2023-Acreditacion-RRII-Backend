<?php

namespace App\Services;

use App\Models\User;

use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Services\GoogleDriveService;

use Illuminate\Http\Request;

class UserService
{

    protected $userRepository;
    protected $roleRepository;
    protected $googleDriveService;

    public function __construct(
        UserRepository $userRepository,
        RoleRepository $roleRepository,
        GoogleDriveService $googleDriveService
    ) {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->googleDriveService = new $googleDriveService;
    }

    public function createUser(Request $request)
    {
        $userAuth = auth()->user();

        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }

        if ($this->userRepository->checkIfEmailExists($request->email)) {
            throw new \App\Exceptions\User\EmailAlreadyExistsException();
        }

        if (!$this->roleRepository->checkIfRoleExists($request->role)) {
            throw new \App\Exceptions\User\RoleNotFoundException();
        }
        $this->googleDriveService->shareParentFolder($request->email);
        return $this->userRepository->createUser($request->email, $request->role);
    }
}
