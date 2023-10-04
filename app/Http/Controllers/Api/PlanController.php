<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\PlanModel;
use App\Models\DateModel;
use App\Models\Evidencias;
use App\Models\GoalModel;
use App\Models\ImprovementActionModel;
use App\Models\ObservationModel;
use App\Models\PlanStatusModel;
use App\Models\ProblemOpportunityModel;
use App\Models\RegistrationStatusModel;
use App\Models\ResourceModel;
use App\Models\ResponsibleModel;
use App\Models\RootCauseModel;
use App\Models\SourceModel;
use App\Models\StandardModel;
use App\Models\User;
use PhpParser\PrettyPrinter\Standard;

//plan::where(["id_user" => $id_user, "id" => $id])->exists()
//$year, $semester, $plan_id, Request $request
class PlanController extends Controller
{

    public function permissions(Request $request)
    {

        $data = User::find(1)->hasPermission('plan_update');
        $permisos = User::find(1)->role()->first()->permissions()->get()->makeHidden(["updated_at", "created_at", "pivot"]);
        //$data = RoleModel::find(1)->permissions()->get();
        //return             
        return response([
            "message" => "!Plan de mejora creado exitosamente",
            "data" => $data,
            "permisos" => $permisos
        ], 201);
    }

    /*
		ruta(post): localhost:8000/api/2023/A/plans/
		ruta(post): localhost:8000/api/2023/A/plans/
        datos:
            {
                "code":"OM82-04-2023",
                "name":"Crear plan",
                "opportunity_for_improvement": "MEJORAR MEJORAR",
                "semester_execution": "2023A",
                "user_id": 1,
                "date_id": 1,
                "standard_id": 1,
                "efficacy_evaluation": false,
                "advance": 5,
                "duration": 7,
                "sources": [{"description": "Description for source 1"}],
                "problems_opportunities": [{"description": "Description for problem/opportunity 1"}],
                "root_causes": [{"description": "Description for root cause 1"}],
                "improvement_actions": [{"description": "Description for improvement action 1"}],
                "resources": [{"description": "Description for resource 1"}],
                "goals": [{ "id": 1,"description": "Meta10" }, { "description": "Meta15" },{ "description": "Meta14" }],
                "responsibles": [{"description": "Description for responsible 1"}],
                "observations":[{"description": "Description for observation 1"}],
                "plan_status_id": 2,
                "registration_status_id": 1,
                "updated_at": "2023-09-10T19:38:11.000000Z",
                "created_at": "2023-09-10T19:38:11.000000Z",
                "id": 3
            }
	*/
    public function createPlan($year, $semester, Request $request)
    {
        try{
            $request->validate([
                'code' => [
                    'required','string',
                    function ($attribute, $value, $fail) {
                        if (!preg_match('/^OM\d{2}-\d{2}-\d{4}$/', $value)) {
                            $fail('El formato del código no es válido. Debe ser OMxx-zz-yyyy');
                        }
                    }
                ],
                "name" => "present|string|max:255",
                "opportunity_for_improvement" => "present|string|max:255",
                "semester_execution" => "present|string|max:8", //aaaa-A/B/C/AB
                "advance" => "present|integer",
                "duration" => "present|integer",
                "efficacy_evaluation" => "present|boolean",
                "standard_id" => "required|integer",
                "plan_status_id" => "required|integer",
                "sources" => "present|array|min:1",
                "sources.*.description" => "required|string|min:1",
                "problems_opportunities" => "present|array|min:1",
                "problems_opportunities.*.description" => "required|string|min:1",
                "root_causes" => "present|array|min:1",
                "root_causes.*.description" => "required|string|min:1",
                "improvement_actions" => "present|array|min:1",
                "improvement_actions.*.description" => "required|string|min:1",
                "resources" => "present|array|min:1",
                "resources.*.description" => "required|string|min:1",
                "goals" => "present|array|min:1",
                "goals.*.description" => "required|string|min:1",
                "responsibles" => "present|array|min:1",
                "responsibles.*.description" => "required|string|min:1",
                "observations" => "present|array|min:1",
                "observations.*.description" => "required|string|min:1"
            ]);
        }
        catch(\Illuminate\Validation\ValidationException $e){
            return response()->json(['errors' => $e->errors()], 400);
        }
        //Lógica de negocio

        if (!DateModel::exists($year, $semester)) {
            return response()->json([
                "message" => "No existe Date"
            ], 404);
        }
        if(PlanModel::where('code', $request->code)->where('standard_id', $request->standard_id)->exists()){
            return response()->json([
                "message" => "Ya existe un plan de mejora con este código: ". $request->code
            ], 422);
        }

        $user = auth()->user();

        if (
            $user->isAssignStandard($request->standard_id)
            or $user->isAdmin()
        ) {
            $plan = new PlanModel();
            $plan->code = $request->code;
            $plan->name = $request->name;
            $plan->opportunity_for_improvement = $request->opportunity_for_improvement;
            $plan->semester_execution = $request->semester_execution;
            $plan->advance = $request->advance;
            $plan->duration = $request->duration;
            $plan->efficacy_evaluation = $request->efficacy_evaluation;
            $plan->plan_status_id = $request->plan_status_id;
            $plan->standard_id = $request->standard_id;
            $plan->user_id = $user->id;
            $plan->date_id = DateModel::dateId($year, $semester);
            $plan->registration_status_id = RegistrationStatusModel::registrationActiveId();

            $plan->save();

            /*-------------------------------Fuentes------------------------------*/

            $sources = $request->sources;

            if (isset($sources)) {
                foreach ($sources as $source) {
                    $plan->sources()->create([
                        'description' => $source['description'],
                    ]);
                }
            }
            /*----------------------------Problemas-------------------------------*/

            $problems = $request->problems;

            if (isset($problems)) {
                foreach ($problems as $problem) {
                    $plan->problemsOpportunities()->create([
                        'description' => $problem['description'],
                    ]);
                }
            }
            /*--------------------------------Causas-------------------------------*/

            $root_causes = $request->root_causes;

            if (isset($root_causes)) {
                foreach ($root_causes as $root_cause) {
                    $plan->rootCauses()->create([
                        'description' => $root_cause['description'],
                    ]);
                }
            }
            /*------------------------------Acciones-------------------------------*/

            $actions = $request->actions;

            if (isset($actions)) {
                foreach ($actions as $action) {
                    $plan->improvementActions()->create([
                        'description' => $action['description'],
                    ]);
                }
            }
            /*------------------------------Recursos-------------------------------*/

            $resources = $request->resources;

            if (isset($resources)) {
                foreach ($resources as $resource) {
                    $plan->resources()->create([
                        'description' => $resource['description'],
                    ]);
                }
            }
            /*--------------------------------Metas-------------------------------*/

            $goals = $request->goals;

            if (isset($goals)) {
                foreach ($goals as $goal) {
                    $plan->goals()->create([
                        'description' => $goal['description'],
                    ]);
                }
            }
            /*---------------------------Responsables-------------------------------*/

            $responsibles = $request->responsibles;

            if (isset($responsibles)) {
                foreach ($responsibles as $responsible) {
                    $plan->responsibles()->create([
                        'description' => $responsible['description'],
                    ]);
                }
            }
            /*--------------------------Observaciones-------------------------------*/

            $observations = $request->observations;

            if (isset($observations)) {
                foreach ($observations as $observation) {
                    $plan->observations()->create([
                        'description' => $observation['description'],
                    ]);
                }
            }
            return response()->json([
                "message" => "!Plan de mejora creado exitosamente",
                "data" => $plan
            ], 201);
        } else {
            return response([
                "status" => 0,
                "message" => "!No se encontro el plan o no esta autorizado",
            ], 404);
        }
    }

    /*
		ruta(put): localhost:8000/api/2023/A/plans/{plan_id}
		ruta(put): localhost:8000/api/2023/A/plans/28
		datos:
            {
                "id": 28,
                "code": "OM23-04-2023",
                "name": "Modificar plan",
                "opportunity_for_improvement": "MEJORAR MEJORAR",
                "semester_execution": "2023A",
                "advance": 5,
                "duration": 7,
                "efficacy_evaluation": false,
                "standard_id": 1,
                "plan_status_id": 1,
                "sources": [{"id": 1, "description": "Description for source 1"}],
                "problems_opportunities": [{"id": 1, "description": "Description for problem/opportunity 1"}],
                "root_causes": [{"id": 1, "description": "Description for root cause 1"}],
                "improvement_actions": [{"id": 1, "description": "Description for improvement action 1"}],
                "resources": [{"id": 1, "description": "Description for resource 1"}],
                "goals": [
                    { "id": 1, "description": "Meta10" },
                    { "id": 2, "description": "Meta15" },
                    { "id": 3, "description": "Meta14" }
                ],
                "responsibles": [{"id": 1, "description": "Description for responsible 1"}],
                "observations": [{"id": 1, "description": "Description for observation 1"}]
            }
	*/
    public function updatePlan($year, $semester, $plan_id, Request $request)
    {
        $request->validate([
            "id" => "required|integer",
            "code" => "required",
            "name" => "present|max:255",
            "opportunity_for_improvement" => "present|max:255",
            "semester_execution" => "present|max:8", //aaaa-A/B/C/AB
            "advance" => "present|integer",
            "duration" => "present|integer",
            "efficacy_evaluation" => "present|boolean",
            "standard_id" => "required|integer",
            "plan_status_id" => "required|integer",
            "sources" => "present",
            "sources.*.description" => "required",
            "problems_opportunities" => "present",
            "problems_opportunities.*.description" => "required",
            "root_causes" => "present",
            "root_causes.*.description" => "required",
            "improvement_actions" => "present",
            "improvement_actions.*.description" => "required",
            "resources" => "present",
            "resources.*.description" => "required",
            "goals" => "present",
            "goals.*.description" => "required",
            "responsibles" => "present",
            "responsibles.*.description" => "required",
            "observations" => "present",
            "observations.*.description" => "required"
        ]);
        $user = auth()->user();
        if (
            PlanModel::existsAndActive($plan_id) and $user->isCreatorPlan($plan_id)
            or $user->isAdmin()
        ) {
            $plan = PlanModel::find($plan_id);
            $plan->code = $request->code;
            $plan->name = $request->name;
            $plan->opportunity_for_improvement = $request->opportunity_for_improvement;
            $plan->semester_execution = $request->semester_execution;
            $plan->advance = $request->advance;
            $plan->duration = $request->duration;
            $plan->efficacy_evaluation = $request->efficacy_evaluation;
            $plan->standard_id = $request->standard_id;
            $plan->plan_status_id = $request->plan_status_id;

            $plan->save();

            /*-------------------------------Fuentes------------------------------*/
            //$plan = PlanModel::find($plan_id);
            $sources = $request->sources;
            //Eliminar fuentes que no esten en el Request
            $existingsIds = collect($sources)->pluck('id')->filter();
            //$sources_delete = $plan->sources()->whereNotIn('id', $existingsIds->toArray())->get();
            $sources_delete = $plan->sourcesActive()->whereNotIn('id', $existingsIds);
            //Actualizar fuentes de estandar
            foreach ($sources_delete as $source_delete) {
                $source_delete->deleteRegister();
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
                                "registration_status_id" => RegistrationStatusModel::registrationActiveId(),
                                //"id_plan" => $plan->id
                            ]
                        );
                    } else {
                        $plan->sources()->create([
                            'description' => $source['description'],
                            "registration_status_id" => RegistrationStatusModel::registrationActiveId()
                        ]);
                    }
                }
            }
            /*----------------------------Problemas-------------------------------*/
            $problems = $request->problems;
            //Eliminar problemas que no esten en el Request
            $existingsIds = collect($problems)->pluck('id')->filter();
            $problems_delete = $plan->problemsOpportunitiesActive()->whereNotIn('id', $existingsIds);

            foreach ($problems_delete as $problem_delete) {
                $problem_delete->deleteRegister();
            }

            //Actualizar problemas de estandar
            if (isset($problems)) {
                foreach ($problems as $problem) {
                    if (isset($problem['id'])) {
                        $plan->problemsOpportunities()->updateOrCreate(
                            [
                                "id" => $problem['id']
                            ],
                            [
                                "description" => $problem['description'],
                                "registration_status_id" => RegistrationStatusModel::registrationActiveId(),
                                //"id_plan" => $plan->id
                            ]
                        );
                    } else {
                        $plan->problemsOpportunities()->create([
                            'description' => $problem['description'],
                            "registration_status_id" => RegistrationStatusModel::registrationActiveId()
                        ]);
                    }
                }
            }
            /*--------------------------------Causas-------------------------------*/
            $root_causes = $request->root_causes;
            //Eliminar causas que no esten en el Request
            $existingsIds = collect($root_causes)->pluck('id')->filter();
            $root_causes_delete = $plan->rootCausesActive()->whereNotIn('id', $existingsIds);
            foreach ($root_causes_delete as $root_cause_delete) {
                $root_cause_delete->deleteRegister();
            }
            //Actualizar causas de estandar
            if (isset($root_causes)) {
                foreach ($root_causes as $root_cause) {
                    if (isset($root_cause['id'])) {
                        $plan->rootCauses()->updateOrCreate(
                            [
                                "id" => $root_cause['id']
                            ],
                            [
                                "description" => $root_cause['description'],
                                "registration_status_id" => RegistrationStatusModel::registrationActiveId(),
                                //"id_plan" => $plan->id
                            ]
                        );
                    } else {
                        $plan->rootCauses()->create([
                            'description' => $root_cause['description'],
                            "registration_status_id" => RegistrationStatusModel::registrationActiveId()
                        ]);
                    }
                }
            }
            /*------------------------------Acciones-------------------------------*/
            $actions = $request->actions;
            //Eliminar acciones que no esten en el Request
            $existingsIds = collect($actions)->pluck('id')->filter();
            $actions_delete = $plan->improvementActionsActive()->whereNotIn('id', $existingsIds);
            foreach ($actions_delete as $action_delete) {
                $action_delete->deleteRegister();
            }
            //Actualizar acciones de estandar
            if (isset($actions)) {
                foreach ($actions as $action) {
                    if (isset($action['id'])) {
                        $plan->improvementActions()->updateOrCreate(
                            [
                                "id" => $action['id']
                            ],
                            [
                                "description" => $action['description'],
                                "registration_status_id" => RegistrationStatusModel::registrationActiveId(),
                                //"id_plan" => $plan->id
                            ]
                        );
                    } else {
                        $plan->improvementActions()->create([
                            'description' => $action['description'],
                            "registration_status_id" => RegistrationStatusModel::registrationActiveId()
                        ]);
                    }
                }
            }
            /*------------------------------Recursos-------------------------------*/
            $resources = $request->resources;
            //Eliminar recursos que no esten en el Request
            $existingsIds = collect($resources)->pluck('id')->filter();
            $resources_delete = $plan->resourcesActive()->whereNotIn('id', $existingsIds);
            foreach ($resources_delete as $resource_delete) {
                $resource_delete->deleteRegister();
            }
            //Actualizar recursos de estandar
            if (isset($resources)) {
                foreach ($resources as $resource) {
                    if (isset($resource['id'])) {
                        $plan->resources()->updateOrCreate(
                            [
                                "id" => $resource['id']
                            ],
                            [
                                "description" => $resource['description'],
                                "registration_status_id" => RegistrationStatusModel::registrationActiveId(),
                                //"id_plan" => $plan->id
                            ]
                        );
                    } else {
                        $plan->resources()->create([
                            'description' => $resource['description'],
                            "registration_status_id" => RegistrationStatusModel::registrationActiveId()
                        ]);
                    }
                }
            }
            /*--------------------------------Metas-------------------------------*/
            $goals = $request->goals;
            //Eliminar metas que no esten en el Request
            $existingsIds = collect($goals)->pluck('id')->filter();
            $goals_delete = $plan->goalsActive()->whereNotIn('id', $existingsIds);
            foreach ($goals_delete as $goal_delete) {
                $goal_delete->deleteRegister();
            }

            //Actualizar metas de estandar
            if (isset($goals)) {
                foreach ($goals as $goal) {
                    if (isset($goal['id'])) {
                        $plan->goals()->updateOrCreate(
                            [
                                "id" => $goal['id']
                            ],
                            [
                                "description" => $goal['description'],
                                "registration_status_id" => RegistrationStatusModel::registrationActiveId(),
                                //"id_plan" => $plan->id
                            ]
                        );
                    } else {
                        $plan->goals()->create([
                            'description' => $goal['description'],
                            "registration_status_id" => RegistrationStatusModel::registrationActiveId()
                        ]);
                    }
                }
            }
            /*---------------------------Responsables-------------------------------*/
            $responsibles = $request->responsibles;
            //Eliminar responsables que no esten en el Request
            $existingsIds = collect($responsibles)->pluck('id')->filter();
            $responsibles_delete = $plan->responsiblesActive()->whereNotIn('id', $existingsIds);
            foreach ($responsibles_delete as $responsible_delete) {
                $responsible_delete->deleteRegister();
            }
            //Actualizar responsables de estandar
            if (isset($responsibles)) {
                foreach ($responsibles as $responsible) {
                    if (isset($responsible['id'])) {
                        $plan->responsibles()->updateOrCreate(
                            [
                                "id" => $responsible['id']
                            ],
                            [
                                "description" => $responsible['description'],
                                "registration_status_id" => RegistrationStatusModel::registrationActiveId(),
                                //"id_plan" => $plan->id
                            ]
                        );
                    } else {
                        $plan->responsibles()->create([
                            'description' => $responsible['description'],
                            "registration_status_id" => RegistrationStatusModel::registrationActiveId()
                        ]);
                    }
                }
            }
            /*--------------------------Observaciones-------------------------------*/
            $observations = $request->observations;
            //Eliminar observaciones que no esten en el Request
            $existingsIds = collect($observations)->pluck('id')->filter();
            $observations_delete = $plan->observationsActive()->whereNotIn('id', $existingsIds);

            foreach ($observations_delete as $observation_delete) {
                $observation_delete->deleteRegister();
            }
            //Actualizar observaciones de estandar

            if (isset($observations)) {
                foreach ($observations as $observation) {
                    if (isset($observation['id'])) {
                        $plan->observations()->updateOrCreate(
                            [
                                "id" => $observation['id']
                            ],
                            [
                                "description" => $observation['description'],
                                "registration_status_id" => RegistrationStatusModel::registrationActiveId(),
                                //"id_plan" => $plan->id
                            ]
                        );
                    } else {
                        $plan->observations()->create([
                            'description' => $observation['description'],
                            "registration_status_id" => RegistrationStatusModel::registrationActiveId()
                        ]);
                    }
                }
            }
            return response()->json($plan, 200);
        } else {
            return response([
                "status" => 0,
                "message" => "!No se encontro el plan o no esta autorizado",
            ], 404);
        }
    }

    public function update(Request $request, $plan_id)
    {
        $user_id = auth()->user()->id;
        $user = UserModel::find($user_id);
        if ($user->isCreatorPlan($plan_id) or $user->isAdmin()) {
            //Actualizamos los atributos propios
            $plan = PlanModel::find($plan_id);
            $plan->update([
                "code" => $request->code,
                "name" => $request->name,
                "opportunity_for_improvement" => $request->opportunity_for_improvement,
                "semester_execution" => $request->semester_execution,
                "advance" => $request->advance,
                "duration" => $request->duration,
                "efficacy_evaluation" => $request->efficacy_evaluation
            ]);


            //Actualizar estandar
            /*$estandar = Estandar::find($request->id_estandar);
			if(isset($estandar)){
				$plan->estandars()->associate($estandar);
			}*/
            /*-------------------------------Fuentes------------------------------*/
            //$plan = PlanModel::find($plan_id);
            $sources = $request->sources;
            //Eliminar fuentes que no esten en el Request
            $existingsIds = collect($sources)->pluck('id')->filter();
            $sources_delete = $plan->sources()->whereNotIn('id', $existingsIds)->get();
            //Actualizar fuentes de estandar
            foreach ($sources_delete as $source_delete) {
                $source_delete->deleteRegister();
            }

            if (isset($sources)) {
                foreach ($sources as $source) {
                    $plan->sources()->updateOrCreate(
                        [
                            "id" => $source['id']
                        ],
                        [
                            "description" => $source['description']
                            //"plan_id" => $plan->id
                        ]
                    );
                }
            }
            /*----------------------------Problemas-------------------------------*/
            $problems = $request->problems;
            //Eliminar problemas que no esten en el Request
            $existingsIds = collect($problems)->pluck('id')->filter();
            $problems_delete = $plan->problemsOpportunities()->whereNotIn('id', $existingsIds)->get();

            foreach ($problems_delete as $problem_delete) {
                $problem_delete->deleteRegister();
            }

            //Actualizar problemas de estandar
            if (isset($problems)) {
                foreach ($problems as $problem) {
                    $plan->problemsOpportunities()->updateOrCreate(
                        [
                            "id" => $problem['id']
                        ],
                        [
                            "description" => $problem['description']
                            //"id_plan" => $plan->id
                        ]
                    );
                }
            }
            /*--------------------------------Causas-------------------------------*/
            $root_causes = $request->root_causes;
            //Eliminar causas que no esten en el Request
            $existingsIds = collect($root_causes)->pluck('id')->filter();
            $root_causes_delete = $plan->rootCauses()->whereNotIn('id', $existingsIds)->get();
            foreach ($root_causes_delete as $root_cause_delete) {
                $root_cause_delete->deleteRegister();
            }
            //Actualizar causas de estandar
            if (isset($root_causes)) {
                foreach ($root_causes as $root_cause) {
                    $plan->rootCauses()->updateOrCreate(
                        [
                            "id" => $root_cause['id']
                        ],
                        [
                            "description" => $root_cause['description'],
                            //"id_plan" => $plan->id
                        ]
                    );
                }
            }
            /*------------------------------Acciones-------------------------------*/
            $actions = $request->actions;
            //Eliminar acciones que no esten en el Request
            $existingsIds = collect($actions)->pluck('id')->filter();
            $actions_delete = $plan->improvementActions()->whereNotIn('id', $existingsIds)->get();
            foreach ($actions_delete as $action_delete) {
                $action_delete->deleteRegister();
            }
            //Actualizar acciones de estandar
            if (isset($actions)) {
                foreach ($actions as $action) {
                    $plan->improvementActions()->updateOrCreate(
                        [
                            "id" => $action['id']
                        ],
                        [
                            "description" => $action['description'],
                            //"id_plan" => $plan->id
                        ]
                    );
                }
            }
            /*------------------------------Recursos-------------------------------*/
            $resources = $request->resources;
            //Eliminar recursos que no esten en el Request
            $existingsIds = collect($resources)->pluck('id')->filter();
            $resources_delete = $plan->recursos()->whereNotIn('id', $existingsIds)->get();
            foreach ($resources_delete as $resource_delete) {
                $resource_delete->deleteRegister();
            }
            //Actualizar recursos de estandar
            if (isset($resources)) {
                foreach ($resources as $resource) {
                    $plan->resources()->updateOrCreate(
                        [
                            "id" => $resource['id']
                        ],
                        [
                            "description" => $resource['description'],
                            // "id_plan" => $plan->id
                        ]
                    );
                }
            }
            /*--------------------------------Metas-------------------------------*/
            $goals = $request->goals;
            //Eliminar metas que no esten en el Request
            $existingsIds = collect($goals)->pluck('id')->filter();
            $goals_delete = $plan->goals()->whereNotIn('id', $existingsIds)->get();
            foreach ($goals_delete as $goal_delete) {
                $goal_delete->deleteRegister();
            }
            //Actualizar metas de estandar
            if (isset($goals)) {
                foreach ($goals as $goal) {
                    $plan->goals()->updateOrCreate(
                        [
                            "id" => $goal['id']
                        ],
                        [
                            "description" => $goal['description'],
                            //"id_plan" => $plan->id
                        ]
                    );
                }
            }
            /*---------------------------Responsables-------------------------------*/
            $responsibles = $request->responsibles;
            //Eliminar responsables que no esten en el Request
            $existingsIds = collect($responsibles)->pluck('id')->filter();
            $responsibles_delete = $plan->responsibles()->whereNotIn('id', $existingsIds)->get();
            foreach ($responsibles_delete as $responsible_delete) {
                $responsible_delete->deleteRegister();
            }
            //Actualizar responsables de estandar
            if (isset($responsibles)) {
                foreach ($responsibles as $responsible) {
                    $plan->responsibles()->updateOrCreate(
                        [
                            "id" => $responsible['id']
                        ],
                        [
                            "name" => $responsible['name'],
                            //"id_plan" => $plan->id
                        ]
                    );
                }
            }
            /*--------------------------Observaciones-------------------------------*/
            $observations = $request->observations;
            //Eliminar observaciones que no esten en el Request
            $existingsIds = collect($observations)->pluck('id')->filter();
            $observations_delete = $plan->observations()->whereNotIn('id', $existingsIds)->get();

            foreach ($observations_delete as $observation_delete) {
                $observation_delete->deleteRegister();
            }
            //Actualizar observaciones de estandar

            if (isset($observations)) {
                foreach ($observations as $observation) {
                    $plan->observations()->updateOrCreate(
                        [
                            "id" => $observation['id']
                        ],
                        [
                            "description" => $observation['description'],
                            //"id_plan" => $plan->id
                        ]
                    );
                }
            }
            return response()->json($plan, 200);
        } else {
            return response([
                "message" => "!No se encontro el plan o no esta autorizado",
            ], 404);
        }
    }

    /*
    public function assignPlan(Request $request)
    {
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        if ($user->isAdmin()) {
            $resp = $request->validate([
                'standard_id' => 'required|integer|exists:standards,id',
                'user_id' => 'required|integer|exists:users,id',
                "name" => "required|max:255",
                'code' => [
                    'required',
                    Rule::unique('plans', 'code')->where(function ($query) use ($request) {
                        return $query->where('standard_id', $request->id_estandar);
                    }),
                ],
            ]);

            if ($resp) {
                $plan = new PlanModel();
                $plan->user_id = $request->user_id;
                $plan->standard_id = $request->standard_id;
                $plan->code = $request->code;
                $plan->advance = 0;
                $plan->plan_status_id = "1";
                $plan->name = $request->name;
                $plan->efficacy_evaluation = false;
                $plan->date_id = 5;
                $plan->registration_status_id = 1;
                $plan->save();
                return response([
                    "message" => "!Plan de mejora asignado exitosamente",
                    "plan_id" => $plan->id,
                ], 200);
            } else {
                return response([
                    "message" => "Código ya asignado a un plan de mejora",
                ], 409);
            }
        } else {
            return response([
                "message" => "No tiene permisos para realizar esta acción",
            ], 403);
        }
    }
*/
    //confirmar los datos nesesarios
    public function listPlan($year, $semester, Request $request)
    {
        $user = auth()->user();
        $query = PlanModel::where('plans.date_id', DateModel::dateId($year, $semester))
            ->where('plans.registration_status_id', RegistrationStatusModel::registrationActive())
            ->select(
                'plans.id',
                'plans.name',
                'plans.code',
                'plans.advance',
                'plans.user_id',
                'standards.name as standard_name',
                'users.name as user_name',
                'plan_status.description as plan_status'
            )
            ->join('standards', 'plans.standard_id', '=', 'standards.id')
            ->join('users', 'plans.user_id', '=', 'users.id')
            ->join('plan_status', 'plans.plan_status_id', '=', 'plan_status.id')
            ->orderBy('plans.id', 'asc');

        if (StandardModel::find($request->query('standard_id'))->nro_standard == 8) {
            $planAll = $query->get();
        } else {
            $planAll = $query->where('plans.standard_id', $request->query('standard_id'))->get();
        }

        foreach ($planAll as $plan) {
            $plan->isCreator = ($plan->user_id == $user->id);
            unset($plan->user_id);
        }

        return response([
            "message" => "Lista de planes de mejora",
            "data" => $planAll,
        ], 200);
    }



    public function deletePlan($year, $semester, $plan_id)
    {
        $user = auth()->user();
        $plan = PlanModel::find($plan_id);
        if (!$plan) {
            return response()->json([
                "message" => "!No se encontro el plan",
            ], 404);
        }

        if ($user->isCreatorPlan($plan_id) or $user->isAdmin()) {
            $plan->deleteRegister();
            return response()->json([
                "message" => "!Se elimino el plan",
            ], 204); //Sale 204 No Content, no devuelve message
        } else {
            return response()->json([
                "message" => "!No esta autorizado par realizar esta accion",
            ], 403);
        }
    }


    public function showPlan($year, $semester, $plan_id)
    {

        if (PlanModel::existsAndActive($plan_id)) {
            $plan = PlanModel::find($plan_id);
            $plan->sources = SourceModel::where("plan_id", $plan_id)
                ->where('registration_status_id', RegistrationStatusModel::registrationActive())
                ->get(['id', 'description']);
            $plan->problems_opportunities = ProblemOpportunityModel::where("plan_id", $plan_id)
                ->where('registration_status_id', RegistrationStatusModel::registrationActive())
                ->get(['id', 'description']);
            $plan->root_causes = RootCauseModel::where("plan_id", $plan_id)
                ->where('registration_status_id', RegistrationStatusModel::registrationActive())
                ->get(['id', 'description']);
            $plan->improvement_actions = ImprovementActionModel::where("plan_id", $plan_id)
                ->where('registration_status_id', RegistrationStatusModel::registrationActive())
                ->get(['id', 'description']);
            $plan->resources = ResourceModel::where("plan_id", $plan_id)
                ->where('registration_status_id', RegistrationStatusModel::registrationActive())
                ->get(['id', 'description']);
            $plan->goals = GoalModel::where("plan_id", $plan_id)
                ->where('registration_status_id', RegistrationStatusModel::registrationActive())
                ->get(['id', 'description']);
            $plan->observations = ObservationModel::where("plan_id", $plan_id)
                ->where('registration_status_id', RegistrationStatusModel::registrationActive())
                ->get(['id', 'description']);
            $plan->responsibles = ResponsibleModel::where("plan_id", $plan_id)
                ->where('registration_status_id', RegistrationStatusModel::registrationActive())
                ->get(['id', 'description']);
            //$plan->evidences = Evidencias::where("id_plan", $plan_id)->get();
            return response([
                "message" => "!Plan de mejora encontrado",
                "data" => $plan,
            ], 200);
        } else {
            return response([
                "message" => "!No se encontro el plan de mejora",
            ], 404);
        }
    }
    /*
    public function showPlanEvidence($id)
    {

        if (plan::where("id", $id)->exists()) {
            $plan = plan::find($id);
            $plan->evidencias = Evidencias::where("id_plan", $id)->get();
            return response([
                "status" => 1,
                "message" => "!Plan de mejora encontrado",
                "data" => $plan,
            ]);
        } else {
            return response([
                "status" => 0,
                "message" => "!No se encontro el plan de mejora",
            ], 404);
        }
    }
*/
    public function listPlanUser($year, $semester)
    {
        $user = auth()->user();
        $planAll = PlanModel::where('plans.registration_status_id', RegistrationStatusModel::registrationActive())
            ->where('plans.date_id', DateModel::dateId($year, $semester))
            ->where("plans.user_id", $user->id)
            ->select(
                'plans.id',
                'plans.name',
                'plans.code',
                'plans.advance',
                'plans.user_id',
                'standards.name as standard_name',
                'users.name as user_name',
                'plan_status.description as plan_status'
            )
            ->join('standards', 'plans.standard_id', '=', 'standards.id')
            ->join('users', 'plans.user_id', '=', 'users.id')
            ->join('plan_status', 'plans.plan_status_id', '=', 'plan_status.id')
            ->orderBy('plans.id', 'asc')
            ->get();

        foreach ($planAll as $plan) {
            $plan->isCreator = ($plan->user_id == $user->id) ? true : false;
            unset($plan->user_id);
        }

        if ($planAll->count() > 0) {
            return response([
                "message" => "!Lista de planes de mejora",
                "data" => $planAll,
            ], 200);
        } else {
            return response([
                "message" => "!No tienes planes de mejora",
                "data" => [],
            ], 404);
        }
    }

    /*$id_user = auth()->user()->id;*/

    public function exportPlan($year, $semester, $plan_id)
    {
        if (PlanModel::where("id", $plan_id)->exists()) {
            $plan = PlanModel::find($plan_id);
            $plan->sources = SourceModel::where("plan_id", $plan_id)->get(['description']);
            $plan->problems_opportunities = ProblemOpportunityModel::where("plan_id", $plan_id)->get(['description']);
            $plan->root_causes = RootCauseModel::where("plan_id", $plan_id)->get(['description']);
            $plan->improvement_actions = ImprovementActionModel::where("plan_id", $plan_id)->get(['description']);
            $plan->resources = ResourceModel::where("plan_id", $plan_id)->get(['description']);
            $plan->goals = GoalModel::where("plan_id", $plan_id)->get(['description']);
            $plan->observations = ObservationModel::where("plan_id", $plan_id)->get(['description']);
            $plan->responsibles = ResponsibleModel::where("plan_id", $plan_id)->get(['description']);
            $plan->evidences = Evidencias::where("id_plan", $plan_id)->get();
            $plan->plan_status = PlanStatusModel::find($plan->plan_status_id)->description;
            try {

                $template = new \PhpOffice\PhpWord\TemplateProcessor('plantilla_plan_de_mejora.docx');

                //1
                $template->setValue('codigo', $plan->code);

                //2
                $content_sources = count($plan->sources) == 0 ?  "No hay fuentes" : "";
                foreach ($plan->sources as $source) {
                    $content_sources .= "- " . $source->description . "</w:t><w:br/><w:t>";
                }
                $content_sources = rtrim($content_sources, "</w:t><w:br/><w:t>");
                $template->setValue('fuentes', $content_sources);

                //3
                $content_problems_opportunities = count($plan->problems_opportunities) == 0 ? "No hay problemas/oportunidades" : "";
                foreach ($plan->problems_opportunities as $problem_opportunity) {
                    $content_problems_opportunities .= "- " . $problem_opportunity->description . "</w:t><w:br/><w:t>";
                }
                $content_problems_opportunities = rtrim($content_problems_opportunities, "</w:t><w:br/><w:t>");
                $template->setValue('problema_oportunidad', $content_problems_opportunities);

                //4
                $content_root_causes = count($plan->root_causes) == 0 ? "No hay causas raices" : "";
                foreach ($plan->root_causes as $root_cause) {
                    $content_root_causes .= "- " . $root_cause->description . "</w:t><w:br/><w:t>";
                }
                $content_root_causes = rtrim($content_root_causes, "</w:t><w:br/><w:t>");
                $template->setValue('causa', $content_root_causes);

                //5
                $template->setValue('oportunidad', $plan->opportunity_for_improvement == null ? "No hay oportunidad plan de mejora" : $plan->opportunity_for_improvement);

                //6
                $content_improvement_actions = count($plan->improvement_actions) == 0 ? "No hay acciones de mejora" : "";
                foreach ($plan->improvement_actions as $improvement_action) {
                    $content_improvement_actions .= "- " . $improvement_action->description . "</w:t><w:br/><w:t>";
                }
                $content_improvement_actions = rtrim($content_improvement_actions, "</w:t><w:br/><w:t>");
                $template->setValue('acciones', $content_improvement_actions);

                //7
                $template->setValue('semestre', $plan->semester_execution == null ? "Sin definir" : $plan->semester_execution);

                //8
                $template->setValue('duracion', $plan->duration == null ? "Sin definir" : $plan->duration);

                //9
                $content_resources = count($plan->resources) == 0 ? "No hay recursos" : "";
                foreach ($plan->resources as $resource) {
                    $content_resources .= "- " . $resource->description . "</w:t><w:br/><w:t>";
                }
                $content_resources = rtrim($content_resources, "</w:t><w:br/><w:t>");
                $template->setValue('recursos', $content_resources);

                //10
                $content_goals = count($plan->goals) == 0 ?  "No hay metas" : "";
                foreach ($plan->goals as $goal) {
                    $content_goals .= "- " . $goal->description . "</w:t><w:br/><w:t>";
                }
                $content_goals = rtrim($content_goals, "</w:t><w:br/><w:t>");
                $template->setValue('metas', $content_goals);

                //11
                $content_responsibles = count($plan->responsibles) == 0 ?  "No hay responsables" : "";
                foreach ($plan->responsibles as $responsible) {
                    $content_responsibles .= "- " . $responsible->description . "</w:t><w:br/><w:t>";
                }
                $content_responsibles = rtrim($content_responsibles, "</w:t><w:br/><w:t>");
                $template->setValue('responsables', $content_responsibles);

                //12
                $content_observations = count($plan->observations) == 0 ? "No hay observaciones" : "";
                foreach ($plan->observations as $observation) {
                    $content_observations .= "- " . $observation->description . "</w:t><w:br/><w:t>";
                }
                $content_observations = rtrim($content_observations, "</w:t><w:br/><w:t>");
                $template->setValue('observaciones', $content_observations);

                //13
                $template->setValue('estado', $plan->plan_status);

                //14
                $content_evidences = count($plan->evidences) == 0 ? "No hay evidencias" : "";
                foreach ($plan->evidences as $evidence) {
                    $content_evidences .= "- " . $evidence->code . "</w:t><w:br/><w:t>";
                }
                $content_evidences = rtrim($content_evidences, "</w:t><w:br/><w:t>");
                $template->setValue('evidencias', $content_evidences);

                //15
                $template->setValue('avance', $plan->advance);

                //16
                $template->setValue('eficacia', $plan->efficacy_evaluation ? "SI" : "NO");

                //Lista de evidencias

                $template->cloneRow('n', count($plan->evidences));
                $i = 1;
                foreach ($plan->evidences as $evidence) {
                    $template->setValue('n#' . $i, $i);
                    $template->setValue('código_e#' . $i, $evidence->code);
                    $template->setValue('denominacion#' . $i, $evidence->denomination);
                    $template->setValue('adjunto#' . $i, "Anexo" . $i);
                    $i++;
                }

                $tempfiledocx = tempnam(sys_get_temp_dir(), 'PHPWord');
                $template->saveAs($tempfiledocx);
                $headers = [
                    'Content-Type' => 'application/msword',
                    'Content-Disposition' => 'attachment;filename="plan.docx"',
                ];
                return response()->download($tempfiledocx, $plan->code . '_plan.docx', $headers);
            } catch (\PhpOffice\PhpWord\Exception\Exception $e) {
                return response([
                    "message" => $e->getMessage(),
                ], 500);
            }
        } else {
            return response([
                "message" => "!No se encontro el plan de mejora",
            ], 404);
        }
    }
    // Arreglar el formato de IDs
    public function createPlanV1(Request $request, $year, $semester)
    {
        $request->validate([
            'code' => [
                'required',
                Rule::unique('plans', 'code')->where(function ($query) use ($request) {
                    return $query->where('standard_id', $request->standard_id);
                }),
            ],
            'name' => 'present|max:255',
            'standard_id' => 'exists:standards,id'
        ]);


        $user = auth()->user();
        $plan = PlanModel::create([
            'code' => $request->code,
            'name' => $request->name,
            'user_id' => $user->id,
            'date_id' => DateModel::date($year, $semester),
            'standard_id' => $request->standard_id
        ]);
        $plan_id = $plan->id;

        foreach ($request->sources as $source) {
            $source_aux = new SourceModel();
            $source_aux->description = $source["description"];
            $source_aux->plan_id = $plan_id;
            $source_aux->save();
        }

        foreach ($request->problems_opportunities as $problem) {
            $problem_opportunity_aux = new ProblemOpportunityModel();
            $problem_opportunity_aux->description = $problem["description"];
            $problem_opportunity_aux->plan_id = $plan_id;
            $problem_opportunity_aux->save();
        }

        foreach ($request->root_causes as $root_cause) {
            $root_cause_aux = new RootCauseModel();
            $root_cause_aux->description = $root_cause["description"];
            $root_cause_aux->plan_id = $plan_id;
            $root_cause_aux->save();
        }

        foreach ($request->improvement_actions as $improvement_action) {
            $improvement_action_aux = new ImprovementActionModel();
            $improvement_action_aux->description = $improvement_action["description"];
            $improvement_action_aux->plan_id = $plan_id;
            $improvement_action_aux->save();
        }

        foreach ($request->resources as $resource) {
            $resource_aux = new ResourceModel();
            $resource_aux->description = $resource["description"];
            $resource_aux->plan_id = $plan_id;
            $resource_aux->save();
        }

        foreach ($request->goals as $goal) {
            $goal_aux = new GoalModel();
            $goal_aux->description = $goal["description"];
            $goal_aux->plan_id = $plan_id;
            $goal_aux->save();
        }

        foreach ($request->observations as $observation) {
            $observation_aux = new ObservationModel();
            $observation_aux->description = $observation["description"];
            $observation_aux->plan_id = $plan_id;
            $observation_aux->save();
        }

        foreach ($request->responsibles as $responsible) {
            $responsible_aux = new ResponsibleModel();
            $responsible_aux->name = $responsible["name"];
            $responsible_aux->plan_id = $plan_id;
            $responsible_aux->save();
        }

        return response([
            "message" => "!Plan de mejora creado exitosamente",
        ], 201);
    }
}
