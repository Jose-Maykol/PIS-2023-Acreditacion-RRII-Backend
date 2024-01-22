<?php

namespace App\Repositories;

use App\Models\DateModel;
use App\Models\RegistrationStatusModel;
use App\Models\StandardModel;
use App\Models\StandardStatusModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StandardRepository
{

    public function getAllUsers()
    {
    }

    public function getStandardById($standard_id)
    {
        return StandardModel::find($standard_id);
    }

    public function getStandardActiveById($standard_id)
    {
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

    public function updateStandardHeader($standard_id, $description, $factor, $dimension, $related_standards)
    {
        $standard = $this->getStandardById($standard_id);
        $standard->description = $description;
        $standard->factor = $factor;
        $standard->dimension = $dimension;
        $standard->related_standards = $related_standards;
        $standard->save();
        return $standard;
    }

    public function listStandardsAssignment($year, $semester)
    {
        //  DB::enableQueryLog();  
        $standards = StandardModel::where('standards.date_id', DateModel::dateId($year, $semester))
            ->select('standards.id', 'standards.name', 'standards.nro_standard', 'standard_status.description as standard_status')
            ->orderBy('standards.nro_standard', 'asc')
            ->leftJoin('standard_status', 'standard_status.id', '=', 'standards.standard_status_id')
            ->with(
                //                'standard_status:id,description',
                'users:id,name,lastname,email'
            )
            ->get();
        //        dd(DB::getQueryLog());
        //$standards = StandardModel::find(1)->standard_status;

        return $standards;
    }

    public function listStandardHeaders($year, $semester)
    {
        $standards = StandardModel::where('standards.date_id', DateModel::dateId($year, $semester))
            ->select('standards.id', 'standards.name', 'standards.nro_standard', 'standards.factor', 'standards.dimension', 'standards.related_standards')
            ->orderBy('standards.nro_standard', 'asc')
            ->get();

        return $standards;
    }

    public function listPartialStandards($year, $semester)
    {
        $standards = StandardModel::where("standards.date_id", DateModel::dateId($year, $semester))
            ->where('standards.registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->select('standards.id', 'standards.name', 'standards.nro_standard')
            ->orderBy('standards.nro_standard', 'asc')
            ->get();

        return $standards;
    }

    public function changeStandardAssignment($standard_id, $users)
    {
        $standard = $this->getStandardById($standard_id);
        return $standard->users()->sync($users);
    }

    public function getFullStandard($standard_id)
    {
        $standard = StandardModel::find($standard_id);
        $standard->standard_status;
        return $standard;
    }

    public function updateStandardStatus($standard_id, $standard_status_id)
    {
        $standard = $this->getStandardById($standard_id);
        $standard->standard_status_id = $standard_status_id;
        $standard->save();
        return $standard;
    }

    //StandardStatus
    public function getAllStandardStatus()
    {
        return StandardStatusModel::all();
    }

    public function getStandardStatusActiveById($standard_status_id)
    {
        return StandardStatusModel::where('id', $standard_status_id)
            ->where('registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->first();
    }
}
