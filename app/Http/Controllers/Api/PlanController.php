<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\PlanModel;
use App\Models\AccionesMejoras;
use App\Models\CausasRaices;
use App\Models\DateModel;
use App\Models\Evidencias;
use App\Models\Fuentes;
use App\Models\GoalModel;
use App\Models\ImprovementActionModel;
use App\Models\Metas;
use App\Models\Observaciones;
use App\Models\ObservationModel;
use App\Models\PlanStatusModel;
use App\Models\ProblemasOportunidades;
use App\Models\ProblemOpportunitieModel;
use App\Models\Recursos;
use App\Models\ResourceModel;
use App\Models\Responsables;
use App\Models\ResponsibleModel;
use App\Models\RoleModel;
use App\Models\RootCauseModel;
use App\Models\SourceModel;
use App\Models\UserModel;

//plan::where(["id_user" => $id_user, "id" => $id])->exists()
class PlanController extends Controller
{

    public function permissions(Request $request) {

        $data = UserModel::find(1)->hasPermission('plan_update');
        $permisos = UserModel::find(1)->role()->first()->permissions()->get()->makeHidden(["updated_at","created_at", "pivot"]);
        //$data = RoleModel::find(1)->permissions()->get();
        //return             
        return response([
            "message" => "!Plan de mejora creado exitosamente",
            "data" => $data,
            "permisos" => $permisos
        ], 201);
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
            foreach ($sources_delete as $source_delete){
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

            foreach ($problems_delete as $problem_delete){
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
            foreach ($root_causes_delete as $root_cause_delete){
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
            foreach ($actions_delete as $action_delete){
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
            foreach ($resources_delete as $resource_delete){
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
            foreach ($goals_delete as $goal_delete){
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
            foreach ($responsibles_delete as $responsible_delete){
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

            foreach ($observations_delete as $observation_delete){
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

    // Arreglar el formato de IDs
    public function createPlan(Request $request, $year, $semester)
    {
        $request->validate([
            'code' => [
                'required',
                Rule::unique('plans', 'code')->where(function ($query) use ($request) {
                    return $query->where('standard_id', $request->id_estandar);
                }),
            ],
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
            "responsibles.*.name" => "required",
            "observations" => "present",
            "observations.*.description" => "required",
            /*      "codigo"=> "required|unique_with:plans,id_estandar|max:11", */            
        ]);

        $user_id = auth()->user()->id;
        $plan = new PlanModel();

        $plan->code = $request->code;
        $plan->name = $request->name;
        $plan->opportunity_for_improvement = $request->opportunity_for_improvement;
        $plan->semester_execution = $request->semester_execution;
        $plan->advance = $request->advance;
        $plan->duration = $request->duration;
        $plan->efficacy_evaluation = $request->efficacy_evaluation;

        $plan->plan_status_id = $request->plan_status_id;
        $plan->standard_id = $request->standard_id;                     //actualizar a id_estandar
        $plan->user_id = $user_id;
        $plan->date_id = DateModel::where('year', $year)->where('semester',$semester)->first()->id;
        $plan->save();

        $plan_id = $plan->id;

        foreach ($request->sources as $source) {
            $source_aux = new SourceModel();
            $source_aux->description = $source["description"];
            $source_aux->plan_id = $plan_id;
            $source_aux->save();
        }

        foreach ($request->problems_opportunities as $problem) {
            $problem_opportunity_aux = new ProblemOpportunitieModel();
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

    public function assignPlan(Request $request)
    {
        $user_id = auth()->user()->id;
        $user = UserModel::find($user_id);
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
                $plan->plan_status = "Planificado";
                $plan->nombre = $request->nombre;
                $plan->evaluacion_eficacia = false;
                $plan->save();
                return response([
                    "status" => 1,
                    "message" => "!Plan de mejora asignado exitosamente",
                    "plan_id" => $plan->id,
                ], 200);
            } else {
                return response([
                    "status" => 0,
                    "message" => "Código ya asignado a un plan de mejora",
                ], 200);
            }
        } else {
            return response([
                "status" => 0,
                "message" => "No tiene permisos para realizar esta acción",
            ], 403);
        }
    }

    //confirmar los datos nesesarios
    public function listPlan()
    {
        $user_id = auth()->user()->id;
        $planAll = PlanModel::select('plans.id', 'plans.name', 'plans.code', 'plans.advance', 'plans.user_id', 
                                    'standards.name as standard_name', 'users.name as user_name', 'plan_status.description as plan_status')
            ->join('standards', 'plans.standard_id', '=', 'standards.id')
            ->join('users', 'plans.user_id', '=', 'users.id')
            ->join('plan_status', 'plans.plan_status_id', '=', 'plan_status.id')
            ->orderBy('plans.id', 'asc')
            ->get();

        foreach ($planAll as $plan) {
            $plan->isCreator = ($plan->user_id == $user_id) ? true : false;
            unset($plan->user_id);
        }
        return response([
            "message" => "!Lista de planes de mejora",
            "data" => $planAll,
        ], 200);
    }
/*
    public function updatePlan(Request $request)
    {
        $request->validate([
            "id" => "required|integer",
            "nombre" => "required|max:255",
            "oportunidad_plan" => "required|max:255",
            "semestre_ejecucion" => "required|max:8",
            "duracion" => "required|integer",
            "estado" => "required|max:30",
            "evaluacion_eficacia" => "required|boolean",
            "avance" => "required|integer",
        ]);
        $id = $request->id;
        $id_user = auth()->user();
        if ($id_user->isCreadorPlan($id) or $id_user->isAdmin()) {
            $plan = plan::find($id);
            $plan->nombre = $request->nombre;
            $plan->oportunidad_plan = $request->oportunidad_plan;
            $plan->semestre_ejecucion = $request->semestre_ejecucion;
            $plan->duracion = $request->duracion;
            $plan->estado = $request->estado;
            $plan->evaluacion_eficacia = $request->evaluacion_eficacia;
            $plan->avance = $request->avance;
            $plan->save();
            return response([
                "status" => 1,
                "message" => "!Plan de mejora actualizado",
                "data" => $plan,
            ]);
        } else {
            return response([
                "status" => 0,
                "message" => "!No se encontro el plan o no esta autorizado",
            ], 404);
        }
    }*/


    public function deletePlan($plan_id)
    {
        $user_id = auth()->user()->id;
        $user = UserModel::find($user_id);
        $plan = PlanModel::find($plan_id);
        if (!$plan) {
            return response([
                "status" => 0,
                "message" => "!No se encontro el plan",
            ], 404);
        }

        if ($user->isCreadorPlan($plan_id) or $user->isAdmin()) {
            $plan->deleteRegister();
            return response([
            ],204);
        } else {
            return response([
                "status" => 0,
                "message" => "!No esta autorizado par realizar esta accion",
            ], 403);
        }
    }

    //faltas completar
    public function showPlan($plan_id)
    {

        if (PlanModel::where("id", $plan_id)->exists()) {
            $plan = PlanModel::find($plan_id);
            $plan->sources = SourceModel::where("plan_id", $plan_id)->get(['id', 'description']);
            $plan->problems_opportunities = ProblemOpportunitieModel::where("plan_id", $plan_id)->get(['id', 'description']);
            $plan->root_causes = RootCauseModel::where("plan_id", $plan_id)->get(['id', 'description']);
            $plan->improvement_actions = ImprovementActionModel::where("plan_id", $plan_id)->get(['id', 'description']);
            $plan->resources = ResourceModel::where("plan_id", $plan_id)->get(['id', 'description']);
            $plan->goals = GoalModel::where("plan_id", $plan_id)->get(['id', 'description']);
            $plan->observations = ObservationModel::where("plan_id", $plan_id)->get(['id', 'description']);
            $plan->responsibles = ResponsibleModel::where("plan_id", $plan_id)->get(['id', 'name']);
            //$plan->evidences = Evidencias::where("id_plan", $plan_id)->get();
            return response([
                "message" => "!Plan de mejora encontrado",
                "data" => $plan,
            ], 200);
        } else {
            return response([
                "status" => 0,
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
    public function listPlanUser()
    {
        $user_id = auth()->user()->id;
        $user = UserModel::find($user_id);
        $planAll = PlanModel::select('plans.id', 'plans.name', 'plans.code', 'plans.advance', 'plans.user_id', 
                                'standards.name as standard_name', 'users.name as user_name', 'plan_status.description as plan_status')
            ->join('standards', 'plans.standard_id', '=', 'standards.id')
            ->join('users', 'plans.user_id', '=', 'users.id')
            ->join('plan_status', 'plans.plan_status_id', '=', 'plan_status.id')
            ->where("plans.user_id", $user_id)
            ->orderBy('plans.id', 'asc')
            ->get();

        foreach ($planAll as $plan) {
            $plan->isCreator = ($plan->user_id == $user_id) ? true : false;
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

    public function exportPlan($plan_id)
    {
        if (PlanModel::where("id", $plan_id)->exists()) {
            $plan = PlanModel::find($plan_id);
            $plan->sources = SourceModel::where("plan_id", $plan_id)->get(['description']);
            $plan->problems_opportunities = ProblemOpportunitieModel::where("plan_id", $plan_id)->get(['description']);
            $plan->root_causes = RootCauseModel::where("plan_id", $plan_id)->get(['description']);
            $plan->improvement_actions = ImprovementActionModel::where("plan_id", $plan_id)->get(['description']);
            $plan->resources = ResourceModel::where("plan_id", $plan_id)->get(['description']);
            $plan->goals = GoalModel::where("plan_id", $plan_id)->get(['description']);
            $plan->observations = ObservationModel::where("plan_id", $plan_id)->get(['description']);
            $plan->responsibles = ResponsibleModel::where("plan_id", $plan_id)->get(['name']);
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
                    $content_responsibles .= "- " . $responsible->name . "</w:t><w:br/><w:t>";
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
}