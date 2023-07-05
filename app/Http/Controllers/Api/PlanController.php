<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\plan;
use App\Models\AccionesMejoras;
use App\Models\CausasRaices;
use App\Models\Evidencias;
use App\Models\Fuentes;
use App\Models\Metas;
use App\Models\Observaciones;
use App\Models\ProblemasOportunidades;
use App\Models\Recursos;
use App\Models\Responsables;



//plan::where(["id_user" => $id_user, "id" => $id])->exists()
class PlanController extends Controller
{
    public function update(Request $request, $id)
    {

        $id_user = auth()->user();
        if ($id_user->isCreadorPlan($id) or $id_user->isAdmin()) {
            //Actualizamos los atributos propios
            $plan = plan::find($id);
            $plan->update([
                //"codigo" => $request->codigo,
                "nombre" => $request->nombre,
                "oportunidad_plan" => $request->oportunidad_plan,
                "semestre_ejecucion" => $request->semestre_ejecucion,
                "duracion" => $request->duracion,
                "estado" => $request->estado,
                "evaluacion_eficacia" => $request->evaluacion_eficacia,
                "avance" => $request->avance,
            ]);

            //Actualizar estandar
            /*$estandar = Estandar::find($request->id_estandar);
			if(isset($estandar)){
				$plan->estandars()->associate($estandar);
			}*/
            /*-------------------------------Fuentes------------------------------*/
            $fuentes = $request->fuentes;
            //Eliminar fuentes que no esten en el Request
            $existingsIds = collect($fuentes)->pluck('id')->filter();
            $plan->fuentes()->whereNotIn('id', $existingsIds)->delete();
            //Actualizar fuentes de estandar
            if (isset($fuentes)) {
                foreach ($fuentes as $fuente) {
                    $plan->fuentes()->updateOrCreate(
                        [
                            "id" => $fuente['id']
                        ],
                        [
                            "descripcion" => $fuente['value'],
                            "id_plan" => $plan->id
                        ]
                    );
                }
            }
            /*----------------------------Problemas-------------------------------*/
            $problemas = $request->problemas;
            //Eliminar problemas que no esten en el Request
            $existingsIds = collect($problemas)->pluck('id')->filter();
            $plan->problemasOportunidade()->whereNotIn('id', $existingsIds)->delete();
            //Actualizar problemas de estandar
            if (isset($problemas)) {
                foreach ($problemas as $problema) {
                    $plan->problemasOportunidade()->updateOrCreate(
                        [
                            "id" => $problema['id']
                        ],
                        [
                            "descripcion" => $problema['value'],
                            "id_plan" => $plan->id
                        ]
                    );
                }
            }
            /*--------------------------------Causas-------------------------------*/
            $causas = $request->causas_raices;
            //Eliminar causas que no esten en el Request
            $existingsIds = collect($causas)->pluck('id')->filter();
            $plan->causasRaices()->whereNotIn('id', $existingsIds)->delete();
            //Actualizar causas de estandar
            if (isset($causas)) {
                foreach ($causas as $causa) {
                    $plan->causasRaices()->updateOrCreate(
                        [
                            "id" => $causa['id']
                        ],
                        [
                            "descripcion" => $causa['value'],
                            "id_plan" => $plan->id
                        ]
                    );
                }
            }
            /*------------------------------Acciones-------------------------------*/
            $acciones = $request->acciones;
            //Eliminar acciones que no esten en el Request
            $existingsIds = collect($acciones)->pluck('id')->filter();
            $plan->accionesMejoras()->whereNotIn('id', $existingsIds)->delete();
            //Actualizar acciones de estandar
            if (isset($acciones)) {
                foreach ($acciones as $accion) {
                    $plan->accionesMejoras()->updateOrCreate(
                        [
                            "id" => $accion['id']
                        ],
                        [
                            "descripcion" => $accion['value'],
                            "id_plan" => $plan->id
                        ]
                    );
                }
            }
            /*------------------------------Recursos-------------------------------*/
            $recursos = $request->recursos;
            //Eliminar recursos que no esten en el Request
            $existingsIds = collect($recursos)->pluck('id')->filter();
            $plan->recursos()->whereNotIn('id', $existingsIds)->delete();
            //Actualizar recursos de estandar
            if (isset($recursos)) {
                foreach ($recursos as $recurso) {
                    $plan->recursos()->updateOrCreate(
                        [
                            "id" => $recurso['id']
                        ],
                        [
                            "descripcion" => $recurso['value'],
                            "id_plan" => $plan->id
                        ]
                    );
                }
            }
            /*--------------------------------Metas-------------------------------*/
            $metas = $request->metas;
            //Eliminar metas que no esten en el Request
            $existingsIds = collect($metas)->pluck('id')->filter();
            $plan->metas()->whereNotIn('id', $existingsIds)->delete();
            //Actualizar metas de estandar
            if (isset($metas)) {
                foreach ($metas as $meta) {
                    $plan->metas()->updateOrCreate(
                        [
                            "id" => $meta['id']
                        ],
                        [
                            "descripcion" => $meta['value'],
                            "id_plan" => $plan->id
                        ]
                    );
                }
            }
            /*---------------------------Responsables-------------------------------*/
            $responsables = $request->responsables;
            //Eliminar responsables que no esten en el Request
            $existingsIds = collect($responsables)->pluck('id')->filter();
            $plan->responsables()->whereNotIn('id', $existingsIds)->delete();
            //Actualizar responsables de estandar
            if (isset($responsables)) {
                foreach ($responsables as $responsable) {
                    $plan->responsables()->updateOrCreate(
                        [
                            "id" => $responsable['id']
                        ],
                        [
                            "nombre" => $responsable['value'],
                            "id_plan" => $plan->id
                        ]
                    );
                }
            }
            /*--------------------------Observaciones-------------------------------*/
            $observaciones = $request->observaciones;
            //Eliminar observaciones que no esten en el Request
            $existingsIds = collect($observaciones)->pluck('id')->filter();
            $plan->observaciones()->whereNotIn('id', $existingsIds)->delete();
            //Actualizar observaciones de estandar
            if (isset($observaciones)) {
                foreach ($observaciones as $observacion) {
                    $plan->observaciones()->updateOrCreate(
                        [
                            "id" => $observacion['id']
                        ],
                        [
                            "descripcion" => $observacion['value'],
                            "id_plan" => $plan->id
                        ]
                    );
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

    // Arreglar el formato de IDs
    public function createPlan(Request $request)
    {
        $request->validate([
            "id_estandar" => "required|integer",
            "nombre" => "present|max:255",
            /*      "codigo"=> "required|unique_with:plans,id_estandar|max:11", */
            'codigo' => [
                'required',
                Rule::unique('plans', 'codigo')->where(function ($query) use ($request) {
                    return $query->where('id_estandar', $request->id_estandar);
                }),
            ],
            "fuentes" => "present",
            "fuentes.*.value" => "required",
            "problemas_oportunidades" => "present",
            "problemas_oportunidades.*.value" => "required",
            "causas_raices" => "present",
            "causas_raices.*.value" => "required",
            "oportunidad_plan" => "present|max:255",
            "acciones_mejoras" => "present",
            "acciones_mejoras.*.value" => "required",
            "semestre_ejecucion" => "present|max:8", //aaaa-A/B/C/AB
            "duracion" => "present|integer",
            "recursos" => "present",
            "recursos.*.value" => "required",
            "metas" => "present",
            "metas.*.value" => "required",
            "responsables" => "present",
            "responsables.*.value" => "required",
            "observaciones" => "present",
            "observaciones.*.value" => "required",
            "estado" => "present|max:30",
            "evaluacion_eficacia" => "present|boolean",
            "avance" => "present|integer"
        ]);

        $id_user = auth()->user()->id;
        $plan = new plan();

        $plan->id_user = $id_user;
        $plan->id_estandar = $request->id_estandar;                     //actualizar a id_estandar

        $plan->nombre = $request->nombre;
        $plan->codigo = $request->codigo;

        $plan->oportunidad_plan = $request->oportunidad_plan;
        $plan->semestre_ejecucion = $request->semestre_ejecucion;
        $plan->duracion = $request->duracion;
        $plan->estado = $request->estado;
        $plan->evaluacion_eficacia = $request->evaluacion_eficacia;
        $plan->avance = $request->avance;
        $plan->save();

        $id_plan = $plan->id;

        foreach ($request->fuentes as $fuente) {
            $fuente_aux = new Fuentes();
            $fuente_aux->descripcion = $fuente["value"];
            $fuente_aux->id_plan = $id_plan;
            $fuente_aux->save();
        }

        foreach ($request->problemas_oportunidades as $problema) {
            $problema_oportunidad_aux = new ProblemasOportunidades();
            $problema_oportunidad_aux->descripcion = $problema["value"];
            $problema_oportunidad_aux->id_plan = $id_plan;
            $problema_oportunidad_aux->save();
        }

        foreach ($request->causas_raices as $causa) {
            $causa_raiz_aux = new CausasRaices();
            $causa_raiz_aux->descripcion = $causa["value"];
            $causa_raiz_aux->id_plan = $id_plan;
            $causa_raiz_aux->save();
        }

        foreach ($request->acciones_mejoras as $accion) {
            $accion_mejora_aux = new AccionesMejoras();
            $accion_mejora_aux->descripcion = $accion["value"];
            $accion_mejora_aux->id_plan = $id_plan;
            $accion_mejora_aux->save();
        }

        foreach ($request->recursos as $recurso) {
            $recurso_aux = new Recursos();
            $recurso_aux->descripcion = $recurso["value"];
            $recurso_aux->id_plan = $id_plan;
            $recurso_aux->save();
        }

        foreach ($request->metas as $meta) {
            $meta_aux = new Metas();
            $meta_aux->descripcion = $meta["value"];
            $meta_aux->id_plan = $id_plan;
            $meta_aux->save();
        }

        foreach ($request->observaciones as $observacion) {
            $observacion_aux = new Observaciones();
            $observacion_aux->descripcion = $observacion["value"];
            $observacion_aux->id_plan = $id_plan;
            $observacion_aux->save();
        }

        foreach ($request->responsables as $responsable) {
            $responsable_aux = new Responsables();
            $responsable_aux->nombre = $responsable["value"];
            $responsable_aux->id_plan = $id_plan;
            $responsable_aux->save();
        }

        return response([
            "status" => 1,
            "message" => "!Plan de mejora creado exitosamente",
        ]);
    }

    public function assignPlan(Request $request)
    {
        $id_user = auth()->user();
        if ($id_user->isAdmin()) {
            $resp = $request->validate([
                'id_estandar' => 'required|integer|exists:estandars,id',
                'id_user' => 'required|integer|exists:users,id',
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
                $plan->nombre = "";
                $plan->evaluacion_eficacia = false;
                $plan->save();
                return response([
                    "status" => 1,
                    "message" => "!Plan de mejora asignado exitosamente",
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
        $id_user = auth()->user()->id;
        $planAll = plan::select('plans.id', 'plans.nombre', 'plans.codigo', 'plans.avance', 'plans.estado', 'plans.id_user', 'estandars.name as estandar_name', 'users.name as user_name')
            ->join('estandars', 'plans.id_estandar', '=', 'estandars.id')
            ->join('users', 'plans.id_user', '=', 'users.id')
            ->orderBy('plans.id', 'asc')
            ->get();

        foreach ($planAll as $plan) {
            $plan->esCreador = ($plan->id_user == $id_user) ? true : false;
            unset($plan->id_user);
        }
        return response([
            "status" => 1,
            "message" => "!Lista de planes de mejora",
            "data" => $planAll,
        ]);
    }

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
