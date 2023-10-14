<?php

namespace App\Repositories;

use App\Models\RegistrationStatusModel;
use App\Models\StandardModel;

class StandardRepository
{

    public function getAllUsers()
    {
    }

    public function getUserById($user_id)
    {
    }


    public function createStandard($email, $role)
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

    public function listStandard(){

    }

    public function checkIfEmailExists($email)
    {
        return User::where('email', $email)->exists();
    }

    public function isAdministrator(User $user){
        return $user->hasRole('administrador');
    }

}