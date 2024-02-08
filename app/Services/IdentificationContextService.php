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

    public function reportContext($year, $semester)
    {
        $contexto = $this->identContRepository->getIdentificationContext($this->dateSemesterRepository->dateId($year, $semester));
        if ($contexto == null) {
            throw new \App\Exceptions\IdentificationContext\ContextNotFoundException();
        } else {
            $tempfiledocx = tempnam(sys_get_temp_dir(), 'PHPWord');
            $template = new \PhpOffice\PhpWord\TemplateProcessor('plantilla-contexto.docx');
            $key = $contexto[0];
            $template->setValue("direccion-sede", $key->address_headquarters);
            $region_province_district = json_encode($key->region_province_district);
            $lugar = json_decode($region_province_district);
            $p = $lugar;
            $template->setValue("región-provincia", $p->region . " / " . $p->province . " / " . $p->district);

            $template->setValue("telefono-institucional", $key->institutional_telephone);
            $template->setValue("página-web", $key->web_page);
            $template->setValue("resolucion", $key->resolution_authorizes_institution);
            $template->setValue("fecha-resolucion", $key->date_resolution);
            $template->setValue("nombre-autoridad-institucion", $key->highest_authority_institution);
            $template->setValue("correo-autoridad-institucion", $key->highest_authority_institution_email);
            $template->setValue("telefono-autoridad-institucion", $key->highest_authority_institution_telephone);

            //Programa de estudios
            $template->setValue("resolucion-programa", $key->licensing_resolution);
            $template->setValue("nivel-academico", $key->academic_level);
            $template->setValue("cui", $key->cui);
            $template->setValue("denominacion-grado", $key->grade_denomination);
            $template->setValue("denominacion-titulo", $key->title_denomination);
            $template->setValue("oferta", $key->authorized_offer);
            $template->setValue("nombre-autoridad-programa", $key->highest_authority_study_program);
            $template->setValue("correo-autoridad-programa", $key->highest_authority_study_program_email);
            $template->setValue("telefono-autoridad-programa", $key->highest_authority_study_program_telephone);

            //Tabla de miembros de comité
            $miembrosComite = $key->members_quality_committee;
            $template->cloneRow('n-c', count($miembrosComite));
            foreach ($miembrosComite as $i => $miembro) {
                $template->setValue("n-c#" . ($i + 1), ($i + 1));
                $template->setValue("nombre-miembro#" . ($i + 1), $miembro["name"]);
                $template->setValue("cargo-miembro#" . ($i + 1), $miembro["position"]);
                $template->setValue("correo#" . ($i + 1), $miembro["email"]);
                $template->setValue("telefono#" . ($i + 1), $miembro["telephone"]);
            }

            //Tabla de interesados
            $interesados = $key->interest_groups_study_program;
            $template->cloneRow('n-g', count($interesados));
            foreach ($interesados as $j => $miembro) {
                $template->setValue("n-g#" . ($j + 1), ($j + 1));
                $template->setValue("interesado#" . ($j + 1), $miembro["interested"]);
                $template->setValue("requerimiento#" . ($j + 1), $miembro["main_requirement_study_program"]);
                $template->setValue("tipo#" . ($j + 1), $miembro["type"]);
            }

            $template->saveAs($tempfiledocx);
            $headers = [
                'Content-Type' => 'application/msword',
                'Content-Disposition' => 'attachment;filename="contexto.docx"',
            ];
            return response()->download($tempfiledocx, "reporte_identificacion_contexto_{$year}-{$semester}.docx", $headers);
        }
    }
}
