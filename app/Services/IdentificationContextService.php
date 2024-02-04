<?php

namespace App\Services;

use App\Models\IdentificationContextModel;
use App\Repositories\DateSemesterRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Repositories\IdentificationContextRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class IdentificationContextService
{

    protected $userRepository;
    protected $dateSemesterRepository;
    protected $identContRepository;
    public function __construct(IdentificationContextRepository $identContRepository, UserRepository $userRepository, DateSemesterRepository $dateSemesterRepository)
    {
        $this->identContRepository = $identContRepository;
        $this->dateSemesterRepository = $dateSemesterRepository;
        $this->userRepository = $userRepository;
    }

    public function createIdentificationContext($year, $semester, $data)
    {
        $userAuth = auth()->user();
        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }

        if (!$this->dateSemesterRepository->dateSemesterExists2($year, $semester)) {
            throw new \App\Exceptions\DateSemester\DateSemesterNotFoundException();
        }
        $id_date_semester = $this->dateSemesterRepository->dateId($year, $semester);
        $data['date_id'] = $id_date_semester;
        #\Illuminate\Support\Facades\Log::info($data);

        $fields = ['name', 'lastname', 'position', 'email', 'telephone'];
        /* $filteredData = array_map(function ($member) use ($fields) {
            return Arr::only($member, $fields);
        }, $data['members_quality_committee']);

        $data['members_quality_committee'] = $filteredData; */

        $fields = ['interested', 'main_requirement_study_program', 'type'];
        /* $filteredData = array_map(function ($group_study) use ($fields) {
            return Arr::only($group_study, $fields);
        }, $data['interest_groups_study_program']);

        $data['interest_groups_study_program'] = $filteredData; */

        $fields = ['region', 'province', 'district'];
        /* $filteredData = array_map(function ($region) use ($fields) {
            return Arr::only($region, $fields);
        }, $data['region_province_district']);

        $data['region_province_district'] = $filteredData; */

        $ident_cont = $this->identContRepository->createIdentificationContext($data);

        $members = $ident_cont->members_quality_committee;

        foreach ($members as $index => &$member) {
            $member['id'] = $index + 1;
        }

        $ident_cont->members_quality_committee = $members;

        $groups_study = $ident_cont->interest_groups_study_program;

        foreach ($groups_study as $index => &$group_study) {
            $group_study['id'] = $index + 1;
        }

        $ident_cont->interest_groups_study_program = $groups_study;

        return $ident_cont;
    }
    
    public function updateIdentificationContext($year, $semester, $data)
    {
        $userAuth = auth()->user();
        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }

        if (!$this->dateSemesterRepository->dateSemesterExists2($year, $semester)) {
            throw new \App\Exceptions\DateSemester\DateSemesterNotFoundException();
        }
        $date_id = $this->dateSemesterRepository->dateId($year, $semester);

        $fields = ['name', 'lastname', 'position', 'email', 'telephone'];
        /* $filteredData = array_map(function ($member) use ($fields) {
            return Arr::only($member, $fields);
        }, $data['members_quality_committee']);

        $data['members_quality_committee'] = $filteredData;

        $fields = ['interested', 'main_requirement_study_program', 'type'];
        $filteredData = array_map(function ($group_study) use ($fields) {
            return Arr::only($group_study, $fields);
        }, $data['interest_groups_study_program']);

        $data['interest_groups_study_program'] = $filteredData;

        $fields = ['region', 'province', 'district'];
        $filteredData = array_map(function ($region) use ($fields) {
            return Arr::only($region, $fields);
        }, $data['region_province_district']);

        $data['region_province_district'] = $filteredData; */

        $ident_cont = $this->identContRepository->updateIdentificationContext($date_id, $data);

        $members = $ident_cont->members_quality_committee;

        foreach ($members as $index => &$member) {
            $member['id'] = $index + 1;
        }

        $ident_cont->members_quality_committee = $members;

        $groups_study = $ident_cont->interest_groups_study_program;

        foreach ($groups_study as $index => &$group_study) {
            $group_study['id'] = $index + 1;
        }

        $ident_cont->interest_groups_study_program = $groups_study;

        return $ident_cont;
    }
    public function getIdentificationContext($year, $semester)
    {
        $userAuth = auth()->user();
        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }

        if (!$this->dateSemesterRepository->dateSemesterExists2($year, $semester)) {
            throw new \App\Exceptions\DateSemester\DateSemesterNotFoundException();
        }
        $id_date_semester = $this->dateSemesterRepository->dateId($year, $semester);
        $ident_cont = $this->identContRepository->getIdentificationContext($id_date_semester);

        $members = $ident_cont->members_quality_committee;

        foreach ($members as $index => &$member) {
            $member['id'] = $index + 1;
        }

        $ident_cont->members_quality_committee = $members;

        $groups_study = $ident_cont->interest_groups_study_program;

        foreach ($groups_study as $index => &$group_study) {
            $group_study['id'] = $index + 1;
        }

        $ident_cont->interest_groups_study_program = $groups_study;

        return $ident_cont;
    }
}
