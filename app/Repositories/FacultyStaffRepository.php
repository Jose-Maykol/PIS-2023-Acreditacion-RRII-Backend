<?php

namespace App\Repositories;

use App\Models\FacultyStaffModel;

class FacultyStaffRepository
{
    public function createFacultyStaff($data)
    {
        $facultyStaff = new FacultyStaffModel();
        $facultyStaff->date_id = $data['date_id'];
        $facultyStaff->number_extraordinary_professor = $data['number_extraordinary_professor'];
        $facultyStaff->number_ordinary_professor_main = $data['number_ordinary_professor_main'];
        $facultyStaff->number_ordinary_professor_associate = $data['number_ordinary_professor_associate'];
        $facultyStaff->number_ordinary_professor_assistant = $data['number_ordinary_professor_assistant'];
        $facultyStaff->number_contractor_professor = $data['number_contractor_professor'];
        $facultyStaff->ordinary_professor_exclusive_dedication = $data['ordinary_professor_exclusive_dedication'];
        $facultyStaff->ordinary_professor_fulltime = $data['ordinary_professor_fulltime'];
        $facultyStaff->ordinary_professor_parttime = $data['ordinary_professor_parttime'];
        $facultyStaff->contractor_professor_fulltime = $data['contractor_professor_fulltime'];
        $facultyStaff->contractor_professor_parttime = $data['contractor_professor_parttime'];
        $facultyStaff->distinguished_researcher = $data['distinguished_researcher'];
        $facultyStaff->researcher_level_i = $data['researcher_level_i'];
        $facultyStaff->researcher_level_ii = $data['researcher_level_ii'];
        $facultyStaff->researcher_level_iii = $data['researcher_level_iii'];
        $facultyStaff->researcher_level_iv = $data['researcher_level_iv'];
        $facultyStaff->researcher_level_v = $data['researcher_level_v'];
        $facultyStaff->researcher_level_vi = $data['researcher_level_vi'];
        $facultyStaff->researcher_level_vii = $data['researcher_level_vii'];
        $facultyStaff->number_publications_indexed = $data['number_publications_indexed'];
        $facultyStaff->intellectual_property_indecopi = $data['intellectual_property_indecopi'];
        $facultyStaff->number_research_project_inexecution = $data['number_research_project_inexecution'];
        $facultyStaff->number_research_project_completed = $data['number_research_project_completed'];
        $facultyStaff->number_professor_inperson_academic_movility = $data['number_professor_inperson_academic_movility'];
        $facultyStaff->number_professor_virtual_academic_movility = $data['number_professor_virtual_academic_movility'];
        $facultyStaff->number_vacancies = $data['number_vacancies'];
        $facultyStaff->number_applicants = $data['number_applicants'];
        $facultyStaff->number_admitted_candidates = $data['number_admitted_candidates'];
        $facultyStaff->number_enrolled_students = $data['number_enrolled_students'];
        $facultyStaff->number_graduates = $data['number_graduates'];
        $facultyStaff->number_alumni = $data['number_alumni'];
        $facultyStaff->number_degree_recipients = $data['number_degree_recipients'];
        $facultyStaff->save();
        return  $facultyStaff;
    }
    public function updateFacultyStaff($date_id, $data)
    {
        $facultyStaff = FacultyStaffModel::where('date_id',$date_id)->first();
        $facultyStaff->number_extraordinary_professor = $data['number_extraordinary_professor'];
        $facultyStaff->number_ordinary_professor_main = $data['number_ordinary_professor_main'];
        $facultyStaff->number_ordinary_professor_associate = $data['number_ordinary_professor_associate'];
        $facultyStaff->number_ordinary_professor_assistant = $data['number_ordinary_professor_assistant'];
        $facultyStaff->number_contractor_professor = $data['number_contractor_professor'];
        $facultyStaff->ordinary_professor_exclusive_dedication = $data['ordinary_professor_exclusive_dedication'];
        $facultyStaff->ordinary_professor_fulltime = $data['ordinary_professor_fulltime'];
        $facultyStaff->ordinary_professor_parttime = $data['ordinary_professor_parttime'];
        $facultyStaff->contractor_professor_fulltime = $data['contractor_professor_fulltime'];
        $facultyStaff->contractor_professor_parttime = $data['contractor_professor_parttime'];
        $facultyStaff->distinguished_researcher = $data['distinguished_researcher'];
        $facultyStaff->researcher_level_i = $data['researcher_level_i'];
        $facultyStaff->researcher_level_ii = $data['researcher_level_ii'];
        $facultyStaff->researcher_level_iii = $data['researcher_level_iii'];
        $facultyStaff->researcher_level_iv = $data['researcher_level_iv'];
        $facultyStaff->researcher_level_v = $data['researcher_level_v'];
        $facultyStaff->researcher_level_vi = $data['researcher_level_vi'];
        $facultyStaff->researcher_level_vii = $data['researcher_level_vii'];
        $facultyStaff->number_publications_indexed = $data['number_publications_indexed'];
        $facultyStaff->intellectual_property_indecopi = $data['intellectual_property_indecopi'];
        $facultyStaff->number_research_project_inexecution = $data['number_research_project_inexecution'];
        $facultyStaff->number_research_project_completed = $data['number_research_project_completed'];
        $facultyStaff->number_professor_inperson_academic_movility = $data['number_professor_inperson_academic_movility'];
        $facultyStaff->number_professor_virtual_academic_movility = $data['number_professor_virtual_academic_movility'];
        $facultyStaff->number_vacancies = $data['number_vacancies'];
        $facultyStaff->number_applicants = $data['number_applicants'];
        $facultyStaff->number_admitted_candidates = $data['number_admitted_candidates'];
        $facultyStaff->number_enrolled_students = $data['number_enrolled_students'];
        $facultyStaff->number_graduates = $data['number_graduates'];
        $facultyStaff->number_alumni = $data['number_alumni'];
        $facultyStaff->number_degree_recipients = $data['number_degree_recipients'];
        $facultyStaff->save();
        return $facultyStaff;
    }
    public function getFacultyStaffId($id_faculty_staff)
    {
        $facultyStaff = FacultyStaffModel::find($id_faculty_staff);
        return $facultyStaff;
    }
    public function getFacultyStaff($id_date_semester)
    {
        $facultyStaff = FacultyStaffModel::where('date_id', $id_date_semester)->get();
        return $facultyStaff;
    }
}
