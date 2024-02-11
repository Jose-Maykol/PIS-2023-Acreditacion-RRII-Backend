<?php

namespace App\Services;

use App\Models\DateModel;
use App\Models\PlanModel;
use App\Models\RegistrationStatusModel;
use App\Models\StandardModel;
use App\Repositories\DateSemesterRepository;
use App\Repositories\PlanRepository;
use App\Repositories\StandardRepository;
use App\Repositories\UserRepository;

use Illuminate\Http\Request;
use PhpParser\PrettyPrinter\Standard;

class PlanService
{
 
    protected $planRepository;
    protected $userRepository;
    protected $standardRepository;
    protected $standardService;
    protected $dateRepository;
    public function __construct(DateSemesterRepository $dateRepository, StandardService $standardService, PlanRepository $planRepository, StandardRepository $standardRepository, UserRepository $userRepository)
    {
        $this->dateRepository = $dateRepository;
        $this->standardService = $standardService;
        $this->planRepository = $planRepository;
        $this->standardRepository = $standardRepository;
        $this->userRepository = $userRepository;
    }

    public function createPlan($year, $semester, Request $request)
    {

        $userAuth = auth()->user();
        if (!($this->userRepository->checkIfUserIsManagerStandard($request->standard_id, $userAuth) 
                or $this->userRepository->isAdministrator($userAuth))) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }

        if (!$this->standardRepository->getStandardActiveById($request->standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }

        if ($this->planRepository->checkIfCodeExists($request->code, $request->standard_id)) {
            throw new \App\Exceptions\Plan\PlanCodeAlreadyExistsException();
        }

        if (!$this->planRepository->getPlanStatusActiveById($request->plan_status_id)) {
            throw new \App\Exceptions\Plan\PlanStatusNotFoundException();
        }

        $plan = $this->planRepository->createPlan($request->code, $request->name, $request->opportunity_for_improvement, $request->semester_execution, $request->advance, $request->duration, $request->efficacy_evaluation, $request->plan_status_id, $request->standard_id, $userAuth->id, DateModel::dateId($year, $semester), RegistrationStatusModel::registrationActiveId());
        
        $this->planRepository->createPlanSources($plan, $request->sources);
        $this->planRepository->createPlanRootCauses($plan, $request->root_causes);
        $this->planRepository->createPlanResponsibles($plan, $request->responsibles);
        $this->planRepository->createPlanResources($plan, $request->resources);
        $this->planRepository->createPlanProblems($plan, $request->problems_opportunities);
        $this->planRepository->createPlanObservations($plan, $request->observations);
        $this->planRepository->createPlanGoals($plan, $request->goals);
        $this->planRepository->createPlanActions($plan, $request->improvement_actions);

        return $plan;

    }

    public function updatePlan($plan_id, Request $request)
    {

        $userAuth = auth()->user();
        if (!($this->userRepository->checkIfUserIsManagerStandard($request->standard_id, $userAuth) 
                or $this->userRepository->isAdministrator($userAuth))) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }

        if (!$this->standardRepository->getStandardActiveById($request->standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }

        if (!$this->planRepository->getPlanActiveById($plan_id)) {
            throw new \App\Exceptions\Plan\PlanNotFoundException();
        }

        if (!$this->planRepository->getPlanStatusActiveById($request->plan_status_id)) {
            throw new \App\Exceptions\Plan\PlanStatusNotFoundException();
        }

        if (($this->planRepository->checkIfCodeExistsInPlan($plan_id, $request->code)
            and !$this->planRepository->checkIfCodeExists($request->code, $request->standard_id))) {
            throw new \App\Exceptions\Plan\PlanCodeAlreadyExistsException();
        }

        

        $plan = $this->planRepository->updatePlan($plan_id, $request->code, $request->name, $request->opportunity_for_improvement,  $request->semester_execution, $request->advance, $request->duration, $request->efficacy_evaluation, $request->plan_status_id);

        $this->planRepository->updatePlanSources($plan, $request->sources);
        $this->planRepository->updatePlanRootCauses($plan, $request->root_causes);
        $this->planRepository->updatePlanResponsibles($plan, $request->responsibles);
        $this->planRepository->updatePlanResources($plan, $request->resources);
        $this->planRepository->updatePlanProblems($plan, $request->problems_opportunities);
        $this->planRepository->updatePlanObservations($plan, $request->observations);
        $this->planRepository->updatePlanGoals($plan, $request->goals);
        $this->planRepository->updatePlanActions($plan, $request->improvement_actions);

        return $plan;

    }

    public function listPlan($year, $semester, Request $request){
        
        if (!$this->standardRepository->getStandardActiveById($request->standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }

        $plans = null;
        if(StandardModel::find($request->standard_id)->nro_standard == 8){
            $plans = $this->planRepository->listPlanAll($year, $semester);
        }
        else{
            $plans = $this->planRepository->listPlanStandard($year, $semester, $request->standard_id);
        }
        $userAuth = auth()->user();
        foreach ($plans as $plan) {
            $plan->isCreator = ($plan->user_id == $userAuth->id);
            unset($plan->user_id);
        }

        return [
            "isSemesterClosed" => $this->dateRepository->isSemesterClosed($year, $semester),
            'plans' => $plans,
            'isManager' => $this->userRepository->checkIfUserIsManagerStandard($request->standard_id, $userAuth)
        ];
    }

    public function deletePlan($plan_id){

        $userAuth = auth()->user();
        if (!$this->planRepository->getPlanActiveById($plan_id)) {
            throw new \App\Exceptions\Plan\PlanNotFoundException();
        }
        $standard_id = $this->planRepository->getStandardForPlan($plan_id);
        if (!($this->userRepository->checkIfUserIsManagerStandard($standard_id, $userAuth) 
                or $this->userRepository->isAdministrator($userAuth))) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        $this->standardService->narrativeIsEnabled($standard_id);


        $plan = $this->planRepository->deletePlan($plan_id);

        return $plan;
    }

    public function showPlan($plan_id){

        if (!$this->planRepository->getPlanActiveById($plan_id)) {
            throw new \App\Exceptions\Plan\PlanNotFoundException();
        }

        $plan = $this->planRepository->showPlan($plan_id);

        return $plan;

    }

    public function listPlanUser($year, $semester, $items, $currentPage, $search){
        $userAuth = auth()->user();

        $plans = $this->planRepository->listPlanUser($year, $semester, $userAuth->id, $items, $currentPage, $search);
        foreach ($plans as $plan) {
            $plan->isCreator = ($plan->user_id == $userAuth->id) ? true : false;
            unset($plan->user_id);
        }
        return [
            'plans' => $plans,
            "isSemesterClosed" => $this->dateRepository->isSemesterClosed($year, $semester)
        ];
    }

    public function exportPlanResume($year, $semester){
        $planes = $this->planRepository->getPlansByDate($year, $semester);
        if(!($planes->count() > 0)){
            throw new \App\Exceptions\Plan\PlansNotFoundByDateException();
        } 
        else {
            $suma_planificados = 0;
            $suma_reprogramados = 0;
            $suma_concluidos = 0;
            $suma_proceso = 0;

            $tempfiledocx = tempnam(sys_get_temp_dir(), 'PHPWord');
            $template = new \PhpOffice\PhpWord\TemplateProcessor('resumen_planes_mejorav4.docx');

            $template->cloneRow('n', $planes->count());

            //0 Periodo
            $template->setValue('year', $year);
            $template->setValue('semester', $semester);

            foreach ($planes as $key => $plan){
                
                // Numero
                $template->setValue('n#'. ($key + 1) , ($key + 1));
    
                //1 Código
                $template->setValue('codigo#'. ($key + 1) , $plan->code);

                //2 Denominación
                $template->setValue('name#'. ($key + 1) , $plan->name);

                //3 Plan Status
                //planificado
                if($plan->plan_status_id == 1){
                    $template->setValue('planificado#'. ($key + 1) , "X");
                    $template->setValue('concluido#'. ($key + 1) , "");
                    $template->setValue('proceso#'. ($key + 1) , "");
                    $template->setValue('reprogramado#'. ($key + 1) , "");

                    $suma_planificados++;
                }
                //en proceso = en desarrollo
                if($plan->plan_status_id == 2){
                    $template->setValue('proceso#' . ($key + 1) , "X");
                    $template->setValue('concluido#' . ($key + 1) , "");
                    $template->setValue('planificado#' . ($key + 1) , "");
                    $template->setValue('reprogramado#' . ($key + 1) , "");

                    $suma_proceso++;
                }
                //concluido = completado
                if($plan->plan_status_id == 3){
                    $template->setValue('proceso#' . ($key + 1) , "");
                    $template->setValue('concluido#' . ($key + 1) , "X");
                    $template->setValue('planificado#' . ($key + 1) , "");
                    $template->setValue('reprogramado#' . ($key + 1) , "");

                    $suma_concluidos++;
                }
                //reprogramado = postergado
                if($plan->plan_status_id == 4){
                    $template->setValue('reprogramado#' . ($key + 1) , "X");
                    $template->setValue('concluido#' . ($key + 1) , "");
                    $template->setValue('planificado#' . ($key + 1) , "");
                    $template->setValue('proceso#' . ($key + 1) , "");

                    $suma_reprogramados++;
                }
            }
            //4 Suma finales
            $template->setValue('suma_c', $suma_concluidos);
            $template->setValue('suma_pr', $suma_proceso);
            $template->setValue('suma_r', $suma_reprogramados);
            $template->setValue('suma_pl', $suma_planificados);

            $template->saveAs($tempfiledocx);
            $headers = [
                'Content-Type' => 'application/msword',
                'Content-Disposition' => 'attachment;filename="planes.docx"',
                'status' => 1,
                'message' => "Reporte de resumen de planes de mejora del periodo $year-$semester",
            ];
            return response()
                ->download($tempfiledocx, $year . '-' . $semester . '_resumen_planes.docx', $headers);
        }
    }

}
