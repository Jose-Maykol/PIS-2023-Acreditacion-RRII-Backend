<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanRequest;
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
use App\Services\PlanService;
use Exception;
use PhpParser\PrettyPrinter\Standard;

//plan::where(["id_user" => $id_user, "id" => $id])->exists()
//$year, $semester, $plan_id, Request $request
class PlanController extends Controller
{
    protected $planService;

    public function __construct(PlanService $planService)
    {

        $this->planService = $planService;
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
    public function createPlan($year, $semester, PlanRequest $request)
    {
        try {
            $request->validated();
            $result = $this->planService->createPlan($year, $semester, $request);
            return response()->json([
                "status" => 1,
                "message" => "Plan de mejora creado exitosamente",
                "data" => $result
            ], 201);
        } catch (\App\Exceptions\User\UserNotAuthorizedException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (\App\Exceptions\Standard\StandardNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (\App\Exceptions\Plan\PlanCodeAlreadyExistsException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        } catch (\App\Exceptions\Plan\PlanStatusNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
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
    public function updatePlan($year, $semester, $plan_id, PlanRequest $request)
    {
        try {
            $request->validated();
            // $result = $this->planService->updatePlan($plan_id, $request);
            return response([
                "status" => 1,
                "message" => "Plan de mejora actualizado exitosamente",
            ], 200);
        } 
        catch (\App\Exceptions\User\UserNotAuthorizedException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
        catch (\App\Exceptions\Standard\StandardNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
        catch (\App\Exceptions\Plan\PlanNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
        catch (\App\Exceptions\Plan\PlanStatusNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
        catch (\App\Exceptions\Plan\PlanCodeAlreadyExistsException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    
    public function listPlan($year, $semester, PlanRequest $request)
    {

        try{
            $request->validated();
            $result = $this->planService->listPlan($year, $semester, $request);
            return response([
                "status" => 1,
                "message" => "Lista de planes de mejora",
                "data" => $result,
            ], 200);
        }
        catch (\App\Exceptions\Standard\StandardNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    public function deletePlan($year, $semester, $plan_id)
    {
        try{
            $result = $this->planService->deletePlan($plan_id);
            return response([
                "status" => 1,
                "message" => "!Se elimino el plan",
                //"data" => $result,
            ], 200);
        }
        catch (\App\Exceptions\Plan\PlanNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
        catch (\App\Exceptions\User\UserNotAuthorizedException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }


    public function showPlan($year, $semester, $plan_id)
    {
        try{
            $result = $this->planService->showPlan($plan_id);
            return response([
                "status" => 1,
                "message" => "!Plan de mejora",
                "data" => $result,
            ], 200);
        }
        catch (\App\Exceptions\Plan\PlanNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
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
        try{
            $result = $this->planService->listPlanUser($year, $semester);
            return response([
                "status" => 1,
                "message" => "!Planes de mejora",
                "data" => $result,
            ], 200);
        }
        catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
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
            //$plan->evidences = Evidencias::where("id_plan", $plan_id)->get();
            $plan->plan_status = PlanStatusModel::find($plan->plan_status_id)->description;
            try {

                $template = new \PhpOffice\PhpWord\TemplateProcessor('plantilla_plan_de_mejora.docx');

                //1 Código
                $template->setValue('codigo', $plan->code);

                //2 Fuentes/Sources
                $content_sources = count($plan->sources) == 0 ?  "No hay fuentes" : "";
                foreach ($plan->sources as $source) {
                    $content_sources .= "- " . $source->description . "</w:t><w:br/><w:t>";
                }
                $content_sources = rtrim($content_sources, "</w:t><w:br/><w:t>");
                $template->setValue('fuentes', $content_sources);

                //3 Problema/Oportunidad
                $content_problems_opportunities = count($plan->problems_opportunities) == 0 ? "No hay problemas/oportunidades" : "";
                foreach ($plan->problems_opportunities as $problem_opportunity) {
                    $content_problems_opportunities .= "- " . $problem_opportunity->description . "</w:t><w:br/><w:t>";
                }
                $content_problems_opportunities = rtrim($content_problems_opportunities, "</w:t><w:br/><w:t>");
                $template->setValue('problema_oportunidad', $content_problems_opportunities);

                //4 Causa/Raiz
                $content_root_causes = count($plan->root_causes) == 0 ? "No hay causas raices" : "";
                foreach ($plan->root_causes as $root_cause) {
                    $content_root_causes .= "- " . $root_cause->description . "</w:t><w:br/><w:t>";
                }
                $content_root_causes = rtrim($content_root_causes, "</w:t><w:br/><w:t>");
                $template->setValue('causa', $content_root_causes);

                //5 Oportunidad de mejora
                $template->setValue('oportunidad', $plan->opportunity_for_improvement == null ? "No hay oportunidad plan de mejora" : $plan->opportunity_for_improvement);

                //6 Acciones de mejora
                $content_improvement_actions = count($plan->improvement_actions) == 0 ? "No hay acciones de mejora" : "";
                foreach ($plan->improvement_actions as $improvement_action) {
                    $content_improvement_actions .= "- " . $improvement_action->description . "</w:t><w:br/><w:t>";
                }
                $content_improvement_actions = rtrim($content_improvement_actions, "</w:t><w:br/><w:t>");
                $template->setValue('acciones', $content_improvement_actions);

                //7 Semestre de ejecución
                $template->setValue('semestre', $plan->semester_execution == null ? "Sin definir" : $plan->semester_execution);

                //8 Duración
                $template->setValue('duracion', $plan->duration == null ? "Sin definir" : $plan->duration);

                //9 Recursos
                $content_resources = count($plan->resources) == 0 ? "No hay recursos" : "";
                foreach ($plan->resources as $resource) {
                    $content_resources .= "- " . $resource->description . "</w:t><w:br/><w:t>";
                }
                $content_resources = rtrim($content_resources, "</w:t><w:br/><w:t>");
                $template->setValue('recursos', $content_resources);

                //10 Metas
                $content_goals = count($plan->goals) == 0 ?  "No hay metas" : "";
                foreach ($plan->goals as $goal) {
                    $content_goals .= "- " . $goal->description . "</w:t><w:br/><w:t>";
                }
                $content_goals = rtrim($content_goals, "</w:t><w:br/><w:t>");
                $template->setValue('metas', $content_goals);

                //11 Responsables
                $content_responsibles = count($plan->responsibles) == 0 ?  "No hay responsables" : "";
                foreach ($plan->responsibles as $responsible) {
                    $content_responsibles .= "- " . $responsible->description . "</w:t><w:br/><w:t>";
                }
                $content_responsibles = rtrim($content_responsibles, "</w:t><w:br/><w:t>");
                $template->setValue('responsables', $content_responsibles);

                //12 Observaciones
                $content_observations = count($plan->observations) == 0 ? "No hay observaciones" : "";
                foreach ($plan->observations as $observation) {
                    $content_observations .= "- " . $observation->description . "</w:t><w:br/><w:t>";
                }
                $content_observations = rtrim($content_observations, "</w:t><w:br/><w:t>");
                $template->setValue('observaciones', $content_observations);

                //13 Estado
                $template->setValue('estado', $plan->plan_status);

                //14 Evidencias
                /*                 $content_evidences = count($plan->evidences) == 0 ? "No hay evidencias" : "";
                foreach ($plan->evidences as $evidence) {
                    $content_evidences .= "- " . $evidence->code . "</w:t><w:br/><w:t>";
                }
                $content_evidences = rtrim($content_evidences, "</w:t><w:br/><w:t>");
                $template->setValue('evidencias', $content_evidences); */

                //15 Avance
                $template->setValue('avance', $plan->advance);

                //16 Eficacia
                $template->setValue('eficacia', $plan->efficacy_evaluation ? "SI" : "NO");

                //Lista de evidencias

                /*  $template->cloneRow('n', count($plan->evidences));
                $i = 1;
                foreach ($plan->evidences as $evidence) {
                    $template->setValue('n#' . $i, $i);
                    $template->setValue('código_e#' . $i, $evidence->code);
                    $template->setValue('denominacion#' . $i, $evidence->denomination);
                    $template->setValue('adjunto#' . $i, "Anexo" . $i);
                    $i++;
                } */

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
