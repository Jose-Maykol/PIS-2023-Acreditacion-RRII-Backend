<?php

namespace App\Repositories;

use App\Models\RegistrationStatusModel;
use App\Models\User;

class UserRepository
{

    public function getAllUsers()
    {
    }

    public function getUserById($user_id)
    {
    }


    public function createUser($email, $role)
    {
        $user = new User();
        $user->name = "NOMBRES";
        $user->lastname = "APELLIDOS";
        $user->email = $email;
        $user->password = "null";
        $user->registration_status_id = RegistrationStatusModel::registrationAuthenticationPendingId();
        $user->save();
        $user->assignRole($role);

        return $user;
    }

    public function checkIfEmailExists($email)
    {
        return User::where('email', $email)->exists();
    }

    public function isAdministrator(User $user)
    {
        return $user->hasRole('administrador');
    }

    public function checkIfUserIsManagerStandard($standard_id, User $user)
    {
        return $user->standards()->where('standards.id', $standard_id)->exists();
    }
}
