<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function createUser(User $user){
        return $user->isAdministrator() ? Response::allow() : Response::deny("El usuario no cuenta con permisos para esta acción."); 
    }

    public function listUser(User $user){
        return $user->isAdministrator() ? Response::allow() : Response::deny("El usuario no cuenta con permisos para esta acción."); 
    }

    public function listEnabledUsers(User $user){
        return $user->isAdministrator() ? Response::allow() : Response::deny("El usuario no cuenta con permisos para esta acción."); 
    }

    public function updateUser(User $user){
        return $user->isAdministrator() ? Response::allow() : Response::deny("El usuario no cuenta con permisos para esta acción."); 
    }

    public function updateRole(User $user){
        return $user->isAdministrator() ? Response::allow() : Response::deny("El usuario no cuenta con permisos para esta acción."); 
    }




    
}
