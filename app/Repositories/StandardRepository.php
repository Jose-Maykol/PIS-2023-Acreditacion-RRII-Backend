<?php

namespace App\Repositories;

use App\Models\DateModel;
use App\Models\RegistrationStatusModel;
use App\Models\StandardModel;
use App\Models\StandardStatusModel;
use App\Models\User;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Builder;

class StandardRepository
{

    public function getAllUsers()
    {
    }

    public function getStandardById($standard_id)
    {
        return StandardModel::find($standard_id);
    }

    public function getStandardActiveById($standard_id){
        return StandardModel::where('id', $standard_id)->where('registration_status_id', RegistrationStatusModel::registrationActiveId())
                ->first();
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

    public function listStandardsAssignment($year, $semester)
    {
        $standards = StandardModel::where('standards.date_id', DateModel::dateId($year, $semester))
            ->select('standards.id', 'standards.name', 'standards.nro_standard')
            ->orderBy('standards.nro_standard', 'asc')
            ->with([
                'users' => function (Builder $query) {
                    $query->select('users.id', 'users.name', 'users.lastname', 'users.email');
                }
            ])
            ->get();
        return $standards;
    }

    public function listPartialStandards($year, $semester){
        $standards = StandardModel::where("standards.date_id", DateModel::dateId($year, $semester))
            ->where('standards.registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->select('standards.id', 'standards.name', 'standards.nro_standard')
            ->orderBy('standards.nro_standard', 'asc')
            ->get();

        return $standards;
    }

    public function changeStandardAssignment($standard_id, $users){
        $standard = $this->getStandardById($standard_id);
        return $standard->users()->sync($users);
    }

    public function getFullStandard($standard_id){
        $standard = StandardModel::find($standard_id);
        $standard->status = $standard->standardStatus();
        return $standard;

    }

   

    //StandardStatus
    public function getAllStandardStatus(){
        return StandardStatusModel::all();
    }

}
