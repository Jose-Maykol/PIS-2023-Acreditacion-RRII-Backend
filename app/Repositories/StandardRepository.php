<?php

namespace App\Repositories;

use App\Models\DateModel;
use App\Models\RegistrationStatusModel;
use App\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;

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

    public function listStandardsAssignment($year, $semester){
        $standards = StandardModel::where('standards.date_id', DateModel::dateId($year, $semester))
                                    ->select('standards.id','standards.name','standards.nro_standard')
                                    ->orderBy('standards.nro_standard', 'asc')
                                    ->with(['users'=> function (Builder $query){
                                        $query->select('users.id', 'users.name', 'users.lastname', 'users.email');
                                    }
                                    ])
                                    ->get();
        return $standards;
    }

    public function checkIfEmailExists($email)
    {
        return User::where('email', $email)->exists();
    }

    public function isAdministrator(User $user){
        return $user->hasRole('administrador');
    }

}