<?php

namespace App\Services;

use App\Models\DateModel;
use App\Models\PlanModel;
use App\Models\RegistrationStatusModel;
use App\Repositories\PlanRepository;
use App\Repositories\StandardRepository;
use App\Repositories\UserRepository;

use Illuminate\Http\Request;

class PlanService
{

    protected $planRepository;
    protected $userRepository;
    protected $standardRepository;

    public function __construct(PlanRepository $planRepository, StandardRepository $standardRepository, UserRepository $userRepository)
    {

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
        $this->planRepository->createPlanProblems($plan, $request->problems);
        $this->planRepository->createPlanObservations($plan, $request->observations);
        $this->planRepository->createPlanGoals($plan, $request->goals);
        $this->planRepository->createPlanActions($plan, $request->actions);

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
            and $this->planRepository->checkIfCodeExists($request->code, $request->standard_id))) {
            throw new \App\Exceptions\Plan\PlanCodeAlreadyExistsException();
        }

        

        $plan = $this->planRepository->updatePlan($plan_id, $request->code, $request->name, $request->opportunity_for_improvement,  $request->semester_execution, $request->advance, $request->duration, $request->efficacy_evaluation, $request->plan_status_id);

        $this->planRepository->updatePlanSources($plan, $request->sources);
        $this->planRepository->updatePlanRootCauses($plan, $request->root_causes);
        $this->planRepository->updatePlanResponsibles($plan, $request->responsibles);
        $this->planRepository->updatePlanResources($plan, $request->resources);
        $this->planRepository->updatePlanProblems($plan, $request->problems);
        $this->planRepository->updatePlanObservations($plan, $request->observations);
        $this->planRepository->updatePlanGoals($plan, $request->goals);
        $this->planRepository->updatePlanActions($plan, $request->actions);

        return $plan;

    }

    

}
