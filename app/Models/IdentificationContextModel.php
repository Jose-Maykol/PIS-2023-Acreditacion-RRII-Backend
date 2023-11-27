<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentificationContextModel extends Model
{ 
    use HasFactory;

    protected $table ='identification_context';
    protected $fillable = [
        'date_id',
        'address_headquarters',
        'region_province_district',
        'institutional_telephone',
        'web_page',
        'date_resolution',
        'highest_authority_institution',
		'highest_authority_institution_email',
        'highest_authority_institution_telephone',
        //
        'licensing_resolution',
        'academic_level',
        'cui',
        'grade_denomination',
        'title_denomination',
        'authorized_offer',
        'highest_authority_study_program',
        'highest_authority_study_program_email',
        'highest_authority_study_program_telephone',
        'members_quality_committee',
        'interest_groups_study_program'
    ];
    protected $casts = [
        'members_quality_committee' => 'json',
        'interest_groups_study_program' => 'json',
        'region_province_district' => 'json'
    ];
}
