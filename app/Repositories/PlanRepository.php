<?php

namespace App\Repositories;

use App\Models\DateModel;
use App\Models\GoalModel;
use App\Models\ImprovementActionModel;
use App\Models\ObservationModel;
use App\Models\PlanModel;
use App\Models\PlanStatusModel;
use App\Models\ProblemOpportunityModel;
use App\Models\RegistrationStatusModel;
use App\Models\ResourceModel;
use App\Models\ResponsibleModel;
use App\Models\RootCauseModel;
use App\Models\SourceModel;

class PlanRepository
{
    public function createPlan($code, $name, $opportunity_for_improvement, $semester_execution, $advance, $duration, $efficacy_evaluation, $plan_status_id, $standard_id, $user_id, $date_id, $registration_status_id)
    {
        $plan = new PlanModel();
        $plan->code = $code;
        $plan->name = $name;
        $plan->opportunity_for_improvement = $opportunity_for_improvement;
        $plan->semester_execution = $semester_execution;
        $plan->advance = $advance;
        $plan->duration = $duration;
        $plan->efficacy_evaluation = $efficacy_evaluation;
        $plan->plan_status_id = $plan_status_id;
        $plan->standard_id = $standard_id;
        $plan->user_id = $user_id;
        $plan->date_id = $date_id;
        $plan->registration_status_id = $registration_status_id;

        $plan->save();
        return $plan;
    }

    public function updatePlan($plan_id, $code, $name, $opportunity_for_improvement, $semester_execution, $advance, $duration, $efficacy_evaluation, $plan_status_id)
    {

        $plan = PlanModel::find($plan_id);
        $plan->code = $code;
        $plan->name = $name;
        $plan->opportunity_for_improvement = $opportunity_for_improvement;
        $plan->semester_execution = $semester_execution;
        $plan->advance = $advance;
        $plan->duration = $duration;
        $plan->efficacy_evaluation = $efficacy_evaluation;
        $plan->plan_status_id = $plan_status_id;

        $plan->save();
        return $plan;
    }

    public function listPlanAll($year, $semester){
        return $this->listPlanQuery($year, $semester)->get();
    }

    public function listPlanStandard($year, $semester, $standard_id){
        return $this->listPlanQuery($year, $semester)
            ->where('plans.standard_id', $standard_id)
            ->get();
    }

    protected function listPlanQuery($year, $semester){
        $query = PlanModel::where('plans.date_id', DateModel::dateId($year, $semester))
            ->where('plans.registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->select(
                'plans.id',
                'plans.name',
                'plans.code',
                'plans.advance',
                'plans.user_id',
                'standards.nro_standard as nro_standard',
                'standards.name as standard_name',
                'users.name as user_name',
                'plan_status.description as plan_status'
            )
            ->join('standards', 'plans.standard_id', '=', 'standards.id')
            ->join('users', 'plans.user_id', '=', 'users.id')
            ->join('plan_status', 'plans.plan_status_id', '=', 'plan_status.id')
            ->orderBy('plans.id', 'asc');
        return $query;
    }

    public function deletePlan($plan_id){
        $plan = PlanModel::find($plan_id);
        $plan->update([
            'registration_status_id' => RegistrationStatusModel::registrationInactiveId()
        ]);
        return $plan;
    }

    public function getStandardForPlan($plan_id){
        return PlanModel::find($plan_id)->standard_id;
    }

    public function showPlan($plan_id){
        $fields = ['id', 'description'];
        $plan = PlanModel::find($plan_id);
        $plan->sources = $this->getPlanSources($plan_id, $fields);
        $plan->problems_opportunities = $this->getPlanProblems($plan_id, $fields);
        $plan->root_causes = $this->getPlanRootCauses($plan_id, $fields);
        $plan->improvement_actions = $this->getPlanActions($plan_id, $fields);
        $plan->resources = $this->getPlanResources($plan_id, $fields);
        $plan->goals = $this->getPlanGoals($plan_id, $fields);
        $plan->responsibles = $this->getPlanResponsibles($plan_id, $fields);
        $plan->observations = $this->getPlanObservations($plan_id, $fields);

        return $plan;
    }

    public function checkIfCodeExistsInPlan($plan_id, $code)
    {
        return PlanModel::where('id', $plan_id)
            ->where('registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->where('code', $code)
            ->exists();
    } //V - V = V // F - V = F // F - F = V

    public function checkIfCodeExists($code, $standard_id)
    {
        return PlanModel::where('standard_id', $standard_id)
            ->where('code', $code)
            ->where('registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->exists();
    }

    public function getPlanActiveById($plan_id)
    {
        return PlanModel::where('id', $plan_id)
            ->where('registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->exists();
    }

    //Plan Status
    public function getPlanStatusActiveById($plan_status_id)
    {
        return PlanStatusModel::where('id', $plan_status_id)
            ->where('registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->exists();
    }

    /*-------------------------------Fuentes------------------------------*/

    public function createPlanSources(PlanModel $plan, $sources)
    {
        foreach ($sources as $source) {
            $plan->sources()->create([
                'description' => $source['description'],
            ]);
        }
    }

    public function updatePlanSources(PlanModel $plan, $sources)
    {
        //Eliminar fuentes que no esten en el Request
        $existingsIds = collect($sources)->pluck('id')->filter();
        $sources_delete = $plan->sources()->whereNotIn('id', $existingsIds)->get();
        //Actualizar fuentes de estandar
        foreach ($sources_delete as $source_delete) {
            $source_delete->delete();
        }

        if (isset($sources)) {
            foreach ($sources as $source) {
                if (isset($source['id'])) {
                    $plan->sources()->update(
                        [
                            "id" => $source['id']
                        ],
                        [
                            "description" => $source['description'],
                        ]
                    );
                } else {
                    $plan->sources()->create([
                        'description' => $source['description'],
                    ]);
                }
            }
        }
    }

    public function getPlanSources($plan_id, $fields){
        return SourceModel::where('plan_id', $plan_id)
            ->get($fields);
    }

    /*----------------------------Problemas-------------------------------*/

    public function createPlanProblems(PlanModel $plan, $problems)
    {
        foreach ($problems as $problem) {
            $plan->problemsOpportunities()->create([
                'description' => $problem['description'],
            ]);
        }
    }

    public function updatePlanProblems(PlanModel $plan, $problems)
    {
        //Eliminar problemas que no esten en el Request
        $existingsIds = collect($problems)->pluck('id')->filter();
        $problems_delete = $plan->problemsOpportunities()->whereNotIn('id', $existingsIds)->get();
        //Actualizar problemas de estandar
        foreach ($problems_delete as $problem_delete) {
            $problem_delete->delete();
        }

        if (isset($problems)) {
            foreach ($problems as $problem) {
                if (isset($problem['id'])) {
                    $plan->problemsOpportunities()->update(
                        [
                            "id" => $problem['id']
                        ],
                        [
                            "description" => $problem['description'],
                        ]
                    );
                } else {
                    $plan->problemsOpportunities()->create([
                        'description' => $problem['description'],
                    ]);
                }
            }
        }
    }

    public function getPlanProblems($plan_id, $fields){
        return ProblemOpportunityModel::where('plan_id', $plan_id)
            ->get($fields);
    }

    /*--------------------------------Causas-------------------------------*/

    public function createPlanRootCauses(PlanModel $plan, $root_causes)
    {
        foreach ($root_causes as $root_cause) {
            $plan->rootCauses()->create([
                'description' => $root_cause['description'],
            ]);
        }
    }

    public function updatePlanRootCauses(PlanModel $plan, $root_causes)
    {
        //Eliminar causas que no esten en el Request
        $existingsIds = collect($root_causes)->pluck('id')->filter();
        $root_causes_delete = $plan->rootCauses()->whereNotIn('id', $existingsIds)->get();
        //Actualizar causas de estandar
        foreach ($root_causes_delete as $root_cause_delete) {
            $root_cause_delete->delete();
        }

        if (isset($root_causes)) {
            foreach ($root_causes as $root_cause) {
                if (isset($root_cause['id'])) {
                    $plan->rootCauses()->update(
                        [
                            "id" => $root_cause['id']
                        ],
                        [
                            "description" => $root_cause['description'],
                        ]
                    );
                } else {
                    $plan->rootCauses()->create([
                        'description' => $root_cause['description'],
                    ]);
                }
            }
        }
    }

    public function getPlanRootCauses($plan_id, $fields){
        return RootCauseModel::where('plan_id', $plan_id)
            ->get($fields);
    }

    /*------------------------------Acciones-------------------------------*/

    public function createPlanActions(PlanModel $plan, $actions)
    {
        foreach ($actions as $action) {
            $plan->improvementActions()->create([
                'description' => $action['description'],
            ]);
        }
    }

    public function updatePlanActions(PlanModel $plan, $actions)
    {
        //Eliminar acciones que no esten en el Request
        $existingsIds = collect($actions)->pluck('id')->filter();
        $actions_delete = $plan->improvementActions()->whereNotIn('id', $existingsIds)->get();
        //Actualizar acciones de estandar
        foreach ($actions_delete as $action_delete) {
            $action_delete->delete();
        }

        if (isset($actions)) {
            foreach ($actions as $action) {
                if (isset($action['id'])) {
                    $plan->improvementActions()->update(
                        [
                            "id" => $action['id']
                        ],
                        [
                            "description" => $action['description'],
                        ]
                    );
                } else {
                    $plan->improvementActions()->create([
                        'description' => $action['description'],
                    ]);
                }
            }
        }
    }

    public function getPlanActions($plan_id, $fields){
        return ImprovementActionModel::where('plan_id', $plan_id)
            ->get($fields);
    }

    /*------------------------------Recursos-------------------------------*/

    public function createPlanResources(PlanModel $plan, $resources)
    {
        foreach ($resources as $resource) {
            $plan->resources()->create([
                'description' => $resource['description'],
            ]);
        }
    }

    public function updatePlanResources(PlanModel $plan, $resources)
    {
        //Eliminar recursos que no esten en el Request
        $existingsIds = collect($resources)->pluck('id')->filter();
        $resources_delete = $plan->resources()->whereNotIn('id', $existingsIds)->get();
        //Actualizar recursos de estandar
        foreach ($resources_delete as $resource_delete) {
            $resource_delete->delete();
        }

        if (isset($resources)) {
            foreach ($resources as $resource) {
                if (isset($resource['id'])) {
                    $plan->resources()->update(
                        [
                            "id" => $resource['id']
                        ],
                        [
                            "description" => $resource['description'],
                        ]
                    );
                } else {
                    $plan->resources()->create([
                        'description' => $resource['description'],
                    ]);
                }
            }
        }
    }

    public function getPlanResources($plan_id, $fields){
        return ResourceModel::where('plan_id', $plan_id)
            ->get($fields);
    }

    /*--------------------------------Metas-------------------------------*/

    public function createPlanGoals(PlanModel $plan, $goals)
    {
        foreach ($goals as $goal) {
            $plan->goals()->create([
                'description' => $goal['description'],
            ]);
        }
    }

    public function updatePlanGoals(PlanModel $plan, $goals)
    {
        //Eliminar metas que no esten en el Request
        $existingsIds = collect($goals)->pluck('id')->filter();
        $goals_delete = $plan->goals()->whereNotIn('id', $existingsIds)->get();
        //Actualizar metas de estandar
        foreach ($goals_delete as $goal_delete) {
            $goal_delete->delete();
        }

        if (isset($goals)) {
            foreach ($goals as $goal) {
                if (isset($goal['id'])) {
                    $plan->goals()->update(
                        [
                            "id" => $goal['id']
                        ],
                        [
                            "description" => $goal['description'],
                        ]
                    );
                } else {
                    $plan->goals()->create([
                        'description' => $goal['description'],
                    ]);
                }
            }
        }
    }

    public function getPlanGoals($plan_id, $fields){
        return GoalModel::where('plan_id', $plan_id)
            ->get($fields);
    }

    /*---------------------------Responsables-------------------------------*/

    public function createPlanResponsibles(PlanModel $plan, $responsibles)
    {
        foreach ($responsibles as $responsible) {
            $plan->responsibles()->create([
                'description' => $responsible['description'],
            ]);
        }
    }

    public function updatePlanResponsibles(PlanModel $plan, $responsibles)
    {
        //Eliminar responsables que no esten en el Request
        $existingsIds = collect($responsibles)->pluck('id')->filter();
        $responsibles_delete = $plan->responsibles()->whereNotIn('id', $existingsIds)->get();
        //Actualizar responsables de estandar
        foreach ($responsibles_delete as $responsible_delete) {
            $responsible_delete->delete();
        }

        if (isset($responsibles)) {
            foreach ($responsibles as $responsible) {
                if (isset($responsible['id'])) {
                    $plan->responsibles()->update(
                        [
                            "id" => $responsible['id']
                        ],
                        [
                            "description" => $responsible['description'],
                        ]
                    );
                } else {
                    $plan->responsibles()->create([
                        'description' => $responsible['description'],
                    ]);
                }
            }
        }
    }

    public function getPlanResponsibles($plan_id, $fields){
        return ResponsibleModel::where('plan_id', $plan_id)
            ->get($fields);
    }

    /*--------------------------Observaciones-------------------------------*/

    public function createPlanObservations(PlanModel $plan, $observations)
    {
        foreach ($observations as $observation) {
            $plan->observations()->create([
                'description' => $observation['description'],
            ]);
        }
    }

    public function updatePlanObservations(PlanModel $plan, $observations)
    {
        //Eliminar observaciones que no esten en el Request
        $existingsIds = collect($observations)->pluck('id')->filter();
        $observations_delete = $plan->observations()->whereNotIn('id', $existingsIds)->get();
        //Actualizar responsables de estandar
        foreach ($observations_delete as $observation_delete) {
            $observation_delete->delete();
        }

        if (isset($observations)) {
            foreach ($observations as $observation) {
                if (isset($observation['id'])) {
                    $plan->observations()->update(
                        [
                            "id" => $observation['id']
                        ],
                        [
                            "description" => $observation['description'],
                        ]
                    );
                } else {
                    $plan->observations()->create([
                        'description' => $observation['description'],
                    ]);
                }
            }
        }
    }

    public function getPlanObservations($plan_id, $fields){
        return ObservationModel::where('plan_id', $plan_id)
            ->get($fields);
    }
}
