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
use App\Models\ProblemasOportunidades;
use App\Models\ProblemOpportunitieModel;
use App\Models\Recursos;
use App\Models\ResourceModel;
use App\Models\Responsables;
use App\Models\ResponsibleModel;
use App\Models\RootCauseModel;
use App\Models\SourceModel;

//plan::where(["id_user" => $id_user, "id" => $id])->exists()
class PlanController extends Controller
{

    public function permissions(Request $request) {

        //return             
        return response([
            "message" => "!Plan de mejora creado exitosamente",
        ], 201);
    }
    public function update(Request $request, $plan_id)
    {
        //$id_user = auth()->user();
        //if ($id_user->isCreatorPlan($plan) or $id_user->isAdmin()) {
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
        /*} else {
            return response([
                "status" => 0,
                "message" => "!No se encontro el plan o no esta autorizado",
            ], 404);
        }*/
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

    /*public function assignPlan(Request $request)
    {
        $id_user = auth()->user();
        if ($id_user->isAdmin()) {
            $resp = $request->validate([
                'id_estandar' => 'required|integer|exists:estandars,id',
                'id_user' => 'required|integer|exists:users,id',
                "nombre" => "required|max:255",
                'codigo' => [
                    'required',
                    Rule::unique('plans', 'codigo')->where(function ($query) use ($request) {
                        return $query->where('id_estandar', $request->id_estandar);
                    }),
                ],
            ]);

            if ($resp) {
                $plan = new plan();
                $plan->id_user = $request->id_user;
                $plan->id_estandar = $request->id_estandar;
                $plan->codigo = $request->codigo;
                $plan->avance = 0;
                $plan->estado = "Planificado";
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
    }*/

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
    }


    public function deletePlan($id)
    {
        $id_user = auth()->user();
        $plan = plan::find($id);
        if (!$plan) {
            return response([
                "status" => 0,
                "message" => "!No se encontro el plan",
            ], 404);
        }

        if ($id_user->isCreadorPlan($id) or $id_user->isAdmin()) {
            $plan->delete();
            return response([
                "status" => 1,
                "message" => "!Plan de mejora eliminado",
            ]);
        } else {
            return response([
                "status" => 0,
                "message" => "!No esta autorizado par realizar esta accion",
            ], 404);
        }
    }

    //faltas completar
    public function showPlan($id)
    {

        if (plan::where("id", $id)->exists()) {
            $plan = plan::find($id);
            $plan->fuentes = Fuentes::where("id_plan", $id)->get(['id', 'descripcion as value']);
            $plan->problemas_oportunidades = ProblemasOportunidades::where("id_plan", $id)->get(['id', 'descripcion as value']);
            $plan->causas_raices = CausasRaices::where("id_plan", $id)->get(['id', 'descripcion as value']);
            $plan->acciones_mejoras = AccionesMejoras::where("id_plan", $id)->get(['id', 'descripcion as value']);
            $plan->recursos = Recursos::where("id_plan", $id)->get(['id', 'descripcion as value']);
            $plan->metas = Metas::where("id_plan", $id)->get(['id', 'descripcion as value']);
            $plan->observaciones = Observaciones::where("id_plan", $id)->get(['id', 'descripcion as value']);
            $plan->responsables = Responsables::where("id_plan", $id)->get(['id', 'nombre as value']);
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

    public function listPlanUser()
    {
        $id_user = auth()->user()->id;
        $planAll = plan::select('plans.id', 'plans.nombre', 'plans.codigo', 'plans.avance', 'plans.estado', 'plans.id_user', 'estandars.name as estandar_name', 'users.name as user_name')
            ->join('estandars', 'plans.id_estandar', '=', 'estandars.id')
            ->join('users', 'plans.id_user', '=', 'users.id')
            ->where("plans.id_user", $id_user)
            ->orderBy('plans.id', 'asc')
            ->get();

        foreach ($planAll as $plan) {
            $plan->esCreador = ($plan->id_user == $id_user) ? true : false;
            unset($plan->id_user);
        }

        if ($planAll->count() > 0) {
            return response([
                "status" => 1,
                "message" => "!Lista de planes de mejora",
                "data" => $planAll,
            ], 200);
        } else {
            return response([
                "status" => 0,
                "message" => "!No tienes planes de mejora",
                "data" => [],
            ], 200);
        }
    }

    /*$id_user = auth()->user()->id;*/
/*
    public function exportPlan($id)
    {
        if (plan::where("id", $id)->exists()) {
            $plan = plan::find($id);
            $plan->fuentes = Fuentes::where("id_plan", $id)->get(['descripcion as value']);
            $plan->problemas_oportunidades = ProblemasOportunidades::where("id_plan", $id)->get(['descripcion as value']);
            $plan->causas_raices = CausasRaices::where("id_plan", $id)->get(['descripcion as value']);
            $plan->acciones_mejoras = AccionesMejoras::where("id_plan", $id)->get(['descripcion as value']);
            $plan->recursos = Recursos::where("id_plan", $id)->get(['descripcion as value']);
            $plan->metas = Metas::where("id_plan", $id)->get(['descripcion as value']);
            $plan->observaciones = Observaciones::where("id_plan", $id)->get(['descripcion as value']);
            $plan->responsables = Responsables::where("id_plan", $id)->get(['nombre as value']);
            $plan->evidencias = Evidencias::where("id_plan", $id)->get();
            try {

                $template = new \PhpOffice\PhpWord\TemplateProcessor('plantilla_plan_de_mejora.docx');

                //1
                $template->setValue('codigo', $plan->codigo);

                //2
                $content_fuentes = count($plan->fuentes) == 0 ?  "No hay fuentes" : "";
                foreach ($plan->fuentes as $fuente) {
                    $content_fuentes .= "- " . $fuente->value . "</w:t><w:br/><w:t>";
                }
                $content_fuentes = rtrim($content_fuentes, "</w:t><w:br/><w:t>");
                $template->setValue('fuentes', $content_fuentes);

                //3
                $content_problemas_oportunidades = count($plan->problemas_oportunidades) == 0 ? "No hay problemas/oportunidades" : "";
                foreach ($plan->problemas_oportunidades as $problema_oportunidad) {
                    $content_problemas_oportunidades .= "- " . $problema_oportunidad->value . "</w:t><w:br/><w:t>";
                }
                $content_problemas_oportunidades = rtrim($content_problemas_oportunidades, "</w:t><w:br/><w:t>");
                $template->setValue('problema_oportunidad', $content_problemas_oportunidades);

                //4
                $content_causas_raices = count($plan->causas_raices) == 0 ? "No hay causas raices" : "";
                foreach ($plan->causas_raices as $causa_raiz) {
                    $content_causas_raices .= "- " . $causa_raiz->value . "</w:t><w:br/><w:t>";
                }
                $content_causas_raices = rtrim($content_causas_raices, "</w:t><w:br/><w:t>");
                $template->setValue('causa', $content_causas_raices);

                //5
                $template->setValue('oportunidad', $plan->oportunidad_plan == null ? "No hay oportunidad plan de mejora" : $plan->oportunidad_plan);

                //6
                $content_acciones_mejoras = count($plan->acciones_mejoras) == 0 ? "No hay acciones de mejora" : "";
                foreach ($plan->acciones_mejoras as $accion_mejora) {
                    $content_acciones_mejoras .= "- " . $accion_mejora->value . "</w:t><w:br/><w:t>";
                }
                $content_acciones_mejoras = rtrim($content_acciones_mejoras, "</w:t><w:br/><w:t>");
                $template->setValue('acciones', $content_acciones_mejoras);

                //7
                $template->setValue('semestre', $plan->semestre_ejecucion == null ? "Sin definir" : $plan->semestre_ejecucion);

                //8
                $template->setValue('duracion', $plan->duracion == null ? "Sin definir" : $plan->duracion);

                //9
                $content_recursos = count($plan->recursos) == 0 ? "No hay recursos" : "";
                foreach ($plan->recursos as $recurso) {
                    $content_recursos .= "- " . $recurso->value . "</w:t><w:br/><w:t>";
                }
                $content_recursos = rtrim($content_recursos, "</w:t><w:br/><w:t>");
                $template->setValue('recursos', $content_recursos);

                //10
                $content_metas = count($plan->metas) == 0 ?  "No hay metas" : "";
                foreach ($plan->metas as $meta) {
                    $content_metas .= "- " . $meta->value . "</w:t><w:br/><w:t>";
                }
                $content_metas = rtrim($content_metas, "</w:t><w:br/><w:t>");
                $template->setValue('metas', $content_metas);

                //11
                $content_responsables = count($plan->responsables) == 0 ?  "No hay responsables" : "";
                foreach ($plan->responsables as $responsable) {
                    $content_responsables .= "- " . $responsable->value . "</w:t><w:br/><w:t>";
                }
                $content_responsables = rtrim($content_responsables, "</w:t><w:br/><w:t>");
                $template->setValue('responsables', $content_responsables);

                //12
                $content_observaciones = count($plan->observaciones) == 0 ? "No hay observaciones" : "";
                foreach ($plan->observaciones as $observacion) {
                    $content_observaciones .= "- " . $observacion->value . "</w:t><w:br/><w:t>";
                }
                $content_observaciones = rtrim($content_observaciones, "</w:t><w:br/><w:t>");
                $template->setValue('observaciones', $content_observaciones);

                //13
                $template->setValue('estado', $plan->estado);

                //14
                $content_evidencias = count($plan->evidencias) == 0 ? "No hay evidencias" : "";
                foreach ($plan->evidencias as $evidencia) {
                    $content_evidencias .= "- " . $evidencia->codigo . "</w:t><w:br/><w:t>";
                }
                $content_evidencias = rtrim($content_evidencias, "</w:t><w:br/><w:t>");
                $template->setValue('evidencias', $content_evidencias);

                //15
                $template->setValue('avance', $plan->avance);

                //16
                $template->setValue('eficacia', $plan->evaluacion_eficacia ? "SI" : "NO");

                //Lista de evidencias

                $template->cloneRow('n', count($plan->evidencias));
                $i = 1;
                foreach ($plan->evidencias as $evidencia) {
                    $template->setValue('n#' . $i, $i);
                    $template->setValue('código_e#' . $i, $evidencia->codigo);
                    $template->setValue('denominacion#' . $i, $evidencia->denominacion);
                    $template->setValue('adjunto#' . $i, "Anexo" . $i);
                    $i++;
                }

                $tempfiledocx = tempnam(sys_get_temp_dir(), 'PHPWord');
                $template->saveAs($tempfiledocx);
                $headers = [
                    'Content-Type' => 'application/msword',
                    'Content-Disposition' => 'attachment;filename="plan.docx"',
                ];
                return response()->download($tempfiledocx, $plan->codigo . '_plan.docx', $headers);
            } catch (\PhpOffice\PhpWord\Exception\Exception $e) {
                return response([
                    "status" => 0,
                    "message" => $e->getMessage(),
                ], 404);
            }
        } else {
            return response([
                "status" => 0,
                "message" => "!No se encontro el plan de mejora",
            ], 404);
        }
    }
}
*/