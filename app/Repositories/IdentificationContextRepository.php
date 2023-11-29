<?php

namespace App\Repositories;

use App\Models\Evidence;
use App\Models\Folder;
use Illuminate\Support\Facades\DB;
use App\Models\DateModel;
use App\Models\RegistrationStatusModel;
use App\Models\IdentificationContextModel;

class IdentificationContextRepository
{
    public function createIdentificationContext($data)
    {
        $identificationContext = new IdentificationContextModel();
        $identificationContext->date_id = $data['date_id'];
        $identificationContext->name_institution = $data['name_institution'];
        $identificationContext->address_headquarters = $data['address_headquarters'];
        $identificationContext->region_province_district = $data['region_province_district'];
        $identificationContext->institutional_telephone = $data['institutional_telephone'];
        $identificationContext->web_page = $data['web_page'];
        $identificationContext->resolution_authorizes_institution = $data['resolution_authorizes_institution'];
        $identificationContext->date_resolution = $data['date_resolution'];
        $identificationContext->highest_authority_institution = $data['highest_authority_institution'];
        $identificationContext->highest_authority_institution_email = $data['highest_authority_institution_email'];
        $identificationContext->highest_authority_institution_telephone = $data['highest_authority_institution_telephone'];
        $identificationContext->resolution_authorizing_offering_program = $data['resolution_authorizing_offering_program'];
        $identificationContext->academic_level = $data['academic_level'];
        $identificationContext->cui = $data['cui'];
        $identificationContext->grade_denomination = $data['grade_denomination'];
        $identificationContext->title_denomination = $data['title_denomination'];
        $identificationContext->authorized_offer = $data['authorized_offer'];
        $identificationContext->highest_authority_study_program = $data['highest_authority_study_program'];
        $identificationContext->highest_authority_study_program_email = $data['highest_authority_study_program_email'];
        $identificationContext->highest_authority_study_program_telephone = $data['highest_authority_study_program_telephone'];
        $identificationContext->members_quality_committee = $data['members_quality_committee'];
        $identificationContext->interest_groups_study_program = $data['interest_groups_study_program'];
        $identificationContext->save();
        return  $identificationContext;
    }
    public function updateIdentificationContext($date_id, $data)
    {
        $identificationContext = IdentificationContextModel::where('date_id',$date_id)->first();
        $identificationContext->name_institution = $data['name_institution'];
        $identificationContext->address_headquarters = $data['address_headquarters'];
        $identificationContext->region_province_district = $data['region_province_district'];
        $identificationContext->institutional_telephone = $data['institutional_telephone'];
        $identificationContext->web_page = $data['web_page'];
        $identificationContext->resolution_authorizes_institution = $data['resolution_authorizes_institution'];
        $identificationContext->date_resolution = $data['date_resolution'];
        $identificationContext->highest_authority_institution = $data['highest_authority_institution'];
        $identificationContext->highest_authority_institution_email = $data['highest_authority_institution_email'];
        $identificationContext->highest_authority_institution_telephone = $data['highest_authority_institution_telephone'];
        $identificationContext->resolution_authorizing_offering_program = $data['resolution_authorizing_offering_program'];
        $identificationContext->academic_level = $data['academic_level'];
        $identificationContext->cui = $data['cui'];
        $identificationContext->grade_denomination = $data['grade_denomination'];
        $identificationContext->title_denomination = $data['title_denomination'];
        $identificationContext->authorized_offer = $data['authorized_offer'];
        $identificationContext->highest_authority_study_program = $data['highest_authority_study_program'];
        $identificationContext->highest_authority_study_program_email = $data['highest_authority_study_program_email'];
        $identificationContext->highest_authority_study_program_telephone = $data['highest_authority_study_program_telephone'];
        $identificationContext->members_quality_committee = $data['members_quality_committee'];
        $identificationContext->interest_groups_study_program = $data['interest_groups_study_program'];
        $identificationContext->save();
        return $identificationContext;
    }
    public function getIdentificationContextId($id_iden_cont)
    {
        $identificationContext = IdentificationContextModel::find($id_iden_cont);
        return $identificationContext;
    }
    public function getIdentificationContext($id_date_semester)
    {
        $identificationContext = IdentificationContextModel::where('date_id', $id_date_semester)->get();
        return $identificationContext;
    }
}
