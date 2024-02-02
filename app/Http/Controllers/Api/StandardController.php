<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StandardRequest;
use App\Models\DateModel;
use App\Models\EvidenceModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\StandardModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\RegistrationStatusModel;
use App\Models\StandardStatusModel;
use App\Models\FacultyStaffModel;
use App\Models\FileModel;
use App\Models\FolderModel;
use App\Models\IdentificationContextModel;
use App\Models\UserStandardModel;
use App\Services\EvidenceService;
use App\Services\StandardService;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

//
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;


//require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StandardController extends Controller
{
    protected $standardService;
    protected $evidenceService;

    public function __construct(StandardService $standardService, EvidenceService $evidenceService)
    {

        $this->standardService = $standardService;
        $this->evidenceService = $evidenceService;
    }

    public function createStandard($year, $semester, Request $request)
    {
        $request->validate([
            "name" => "required",
            "factor" => "required",
            "dimension" => "required",
            "related_standards" => "required",
            "nro_standard" => "required|integer",
        ]);
        $standard = new StandardModel();

        $standard->name = $request->name;
        $standard->factor = $request->factor;
        $standard->dimension = $request->dimension;
        $standard->related_standards = $request->related_standards;
        $standard->nro_standard = $request->nro_standard;

        $standard->date_id = DateModel::dateId($year, $semester);
        $standard->registration_status_id = RegistrationStatusModel::registrationActive();

        $standard->save();
        return response([
            "status" => 1,
            "message" => "Estándar creado exitosamente",
            "data" => $standard,
        ], 201);
    }

    public function createStandards($year, $semester, Request $request)
    {
        $request->validate([
            '*.name' => 'required',
            '*.factor' => 'required',
            '*.dimension' => 'required',
            '*.related_standards' => 'required',
            '*.nro_standard' => 'required|integer',
            '*.description' => 'nullable|string',
        ]);

        $standards = [];

        $date_id = DateModel::dateId($year, $semester);
        $standardsExists = StandardModel::where('date_id', $date_id)->exists();

        if ($standardsExists) {
            return response([
                "status" => 0,
                "message" => "Ya existen estándares para este periodo",
            ], 400);
        }

        foreach ($request->all() as $standardData) {
            if ($standardData['description'] == null) {
                $standard->description = "";
            }

            $standard = new StandardModel();

            $standard->name = $standardData['name'];
            $standard->factor = $standardData['factor'];
            $standard->dimension = $standardData['dimension'];
            $standard->description = $standardData['description'];
            $standard->related_standards = $standardData['related_standards'];
            $standard->nro_standard = $standardData['nro_standard'];
            $standard->date_id = DateModel::dateId($year, $semester);
            $standard->registration_status_id = RegistrationStatusModel::registrationActiveId();
            $standard->standard_status_id = 1;
            $standard->save();

            $standards[] = $standard;
        }

        return response([
            'status' => 1,
            'message' => 'Estándares creados exitosamente',
            'data' => $standards,
        ], 201);
    }

    public function listStandardHeaders(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'year' => 'required|integer|digits:4',
            'semester' => 'required|string|in:A,B',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Campos requeridos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {

            $year = $request->input('year');
            $semester = $request->input('semester');


            $result = $this->standardService->listStandardHeaders($year, $semester);
            return response()->json([
                'status' => 1,
                'message' => "Estándares obtenidos",
                'data' => $result
            ], 200);
        } catch (\App\Exceptions\User\UserNotAuthorizedException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    /*
		ruta(get): localhost:8000/api/2023/A/standards
		ruta(get): localhost:8000/api/2023/A/standards/4/narratives
		datos:
			{
				"access_token":"11|s3NwExv5FWC7tmsqFUfyB48KFTM6kajH7A1oN3u3"
			}
	*/

    public function listPartialStandard($year, $semester)
    {
        try {

            $result = $this->standardService->listPartialStandards($year, $semester);
            return response()->json([
                'status' => 1,
                'message' => 'Lista parcial de estándares',
                'data' => $result
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    /*
		ruta(get): localhost:8000/api/2023/A/standards/standard-values
		ruta(get): localhost:8000/api/2023/A/standards/4
		datos:
			{
				"access_token":"11|s3NwExv5FWC7tmsqFUfyB48KFTM6kajH7A1oN3u3"
			}
	*/
    public function listStandardsAssignment($year, $semester)
    {
        try {
            $result = $this->standardService->listStandardsAssignment($year, $semester);
            return response()->json([
                "status" => 1,
                "message" => "Lista de estándares",
                "data" => $result,
            ], 200);
        } catch (\App\Exceptions\User\UserNotAuthorizedException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    public function changeStandardAssignment($year, $semester, $standard_id, StandardRequest $request)
    {
        try {
            $request->validated();
            $result = $this->standardService->changeStandardAssignment($standard_id, $request);
            return response()->json([
                'status' => 1,
                'message' => 'Estándares asignados'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 400);
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
        }
    }

    /*
		ruta(get): localhost:8000/api/2023/A/standards/{standard_id}
		ruta(get): localhost:8000/api/2023/A/standards/1
		datos:
			{
				"access_token":"11|s3NwExv5FWC7tmsqFUfyB48KFTM6kajH7A1oN3u3"
			}
	*/
    public function showStandardHeader($year, $semester, $standard_id, Request $request)
    {
        $result = $this->standardService->showStandard($standard_id);
        return response()->json([
            'status' => 1,
            'message' => "Estándar retornado",
            'data' => $result
        ], 200);
    }

    /*
		ruta(put): localhost:8000/api/2023/A/standards/{standard_id}
		ruta(put): localhost:8000/api/2023/A/standards/1
		datos:
			{
                "name":"E-4 Sostenibilidad(Modificado)",
                "factor":"Uno",
                "dimension":"Uno",
                "related_standards":"dos",
                "nro_standard":"1",
                "access_token":"11|s3NwExv5FWC7tmsqFUfyB48KFTM6kajH7A1oN3u3"
            }
	*/


    public function updateStandardHeader($year, $semester, $standard_id, StandardRequest $request)
    {
        try {
            $request->validated();
            $result = $this->standardService->updateStandardHeader($standard_id, $request);
            return response()->json([
                'status' => 1,
                'message' => 'Estándar modificado exitosamente',
                'data' => $result
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 400);
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
        }
    }

    /*
		ruta(delete): localhost:8000/api/2023/A/standards/{standard_id}
		ruta(delete): localhost:8000/api/2023/A/standards/1
		datos:
			{
				"access_token":"11|s3NwExv5FWC7tmsqFUfyB48KFTM6kajH7A1oN3u3"
			}
	*/

    public function deleteEstandar($standard_id)
    {
        //echo 'ID: ' . $standard_id . '';

        $id_user = auth()->user()->id;

        $user = User::find($id_user);
        if (StandardModel::where(["id" => $standard_id, "user_id" => $user->id])->exists()) { //ERROR:  no existe la columna «user_id»
            $standard = StandardModel::where(["id" => $standard_id, "user_id" => $user->id])->first(); //ERROR:  no existe la columna «user_id»
            $standard->deleteRegister(); //función no implementada
            return response([
                "msg" => "Estándar eliminado",
            ], 204);
        } else {
            return response([
                "msg" => "No se encontro el estándar o no esta autorizado",
            ], 404);
        }
    }

    /*
		ruta(get): localhost:8000/api/2023/A/standards/{standard_id}/evidencias
		ruta(get): localhost:8000/api/2023/A/standards/1/evidencias
		datos:
			{
				"parent_id":"1",
                "access_token":"11|s3NwExv5FWC7tmsqFUfyB48KFTM6kajH7A1oN3u3"
			}
	*/

    public function getStandardEvidences(StandardRequest $request, $year, $semester, $standard_id, $evidence_type_id)
    {
        try {
            $result = $this->standardService->getStandardEvidences($request);
            return response()->json([
                "status" => 1,
                "data" => $result,
            ], 200);
        } catch (\App\Exceptions\Evidence\FolderNotFoundException $e) {
            return response()->json([
                "status" => 0,
                "message" => "Aun no hay evidencias para este estándar",
                "data" => [
                    "evidences" => [],
                    "folders" => []
                ]
            ], $e->getCode());
        }
        
    }

    public function blockNarrative(Request $request)
    {
        try {
            $result = $this->standardService->blockNarrative($request);
            return response()->json([
                "status" => 1,
                "data" => $result,
            ], 200);
        } catch (\App\Exceptions\Standard\NarrativeIsBeingEditingException $e) {
            return response()->json([
                "status" => 0,
                "message" => "La narrativa está siendo editada...",
                "data" => [
                    "user_name" => $e->getMessage()
                ]
            ], $e->getCode());
        }
        
    }

    public function unlockNarrative(Request $request)
    {
        try {
            $result = $this->standardService->unlockNarrative($request);
            return response()->json([
                "status" => 1,
                "data" => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "No se pudo desbloquear la narrativa.",
            ], $e->getCode());
        }
        
    }

    public function enableNarrative(Request $request)
    {
        try {
            $result = $this->standardService->enableNarrative($request);
            return response()->json([
                "status" => 1,
                "data" => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => 0,
                "message" => "No se pudo habilitar la narrativa.",
            ], $e->getCode());
        }
        
    }

    public function searchEvidence($year, $semester, $standard_id)
    {
        try {
            $result = $this->evidenceService->searchEvidence($standard_id);
            return response()->json([
                "status" => 1,
                "data" => $result,
            ], 200);
        } catch (\App\Exceptions\Standard\StandardNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }
    /*
    public function standardStatus($year, $semester, $standard_id)
    {
        if (StandardModel::where('id', $standard_id)->exists()) {
            $standard= StandardModel::where('id', $standard_id)->select('standard_status_id')->first();
            $standardStatusList = StandardStatusModel::select('id', 'description')->get();
            
            $standardStatusList->each(function ($listItem) use ($standard) {
                $listItem->active = $listItem->id == $standard->standard_status_id;
            });

            return response([
                "status" => 1,
                "data" => $standardStatusList
            ], 200);
        } else {
            return response([
                "status" => 0,
                "message" => "No existe el estándar",
            ], 404);
        }
    }
*/
    public function listStandardStatus($year, $semester, $standard_id = 0)
    {
        try {
            $result = $this->standardService->listStandardStatus($standard_id);
            return response()->json([
                'status' => 1,
                'message' => "Lista de estandares",
                'data' => $result
            ], 200);
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
        }
    }

    public function updateStatusStandard($year, $semester, $standard_id, $standard_status_id, Request $request)
    {
        try {
            $result = $this->standardService->updateStandardStatus($standard_id, $standard_status_id);
            return response()->json([
                'status' => 1,
                'message' => "Estandard actualizado",
            ], 200);
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
        } catch (\App\Exceptions\Standard\StandardStatusNotFoundException $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }


    public function listUserAssigned($year, $semester, $standard_id)
    {
        try {
            $result = $this->standardService->listUserAssigned($standard_id);
            return response()->json([
                'status' => 1,
                'message' => "Lista de usuarios del estandar",
                'data' => $result
            ], 200);
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
        }
    }

    public function reportContext($year, $semester)
    {
        $tempfiledocx = tempnam(sys_get_temp_dir(), 'PHPWord');
        $template = new \PhpOffice\PhpWord\TemplateProcessor('plantilla-contexto.docx');
        $contexto = IdentificationContextModel::where("date_id", DateModel::dateId($year, $semester))->get();
        if ($contexto->count() > 0) {
            $key = $contexto[0];
            $template->setValue("direccion-sede", $key->address_headquarters);
            $lugar = json_decode($key->region_province_district);
            $p = $lugar[0];
            $template->setValue("región-provincia", $p->region . " / " . $p->provincia . " / " . $p->distrito);

            $template->setValue("telefono-institucional", $key->institutional_telephone);
            $template->setValue("página-web", $key->web_page);
            $template->setValue("resolucion", "NO SE SABE, no se sabe, hasta yo quiero saber pero no se");
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
                $template->setValue("nombre-miembro#" . ($i + 1), $miembro["Nombre"]);
                $template->setValue("cargo-miembro#" . ($i + 1), $miembro["Cargo"]);
                $template->setValue("correo#" . ($i + 1), $miembro["Correo"]);
                $template->setValue("telefono#" . ($i + 1), $miembro["Teléfono"]);
            }

            //Tabla de interesados
            $interesados = $key->interest_groups_study_program;
            $template->cloneRow('n-g', count($interesados));
            foreach ($interesados as $j => $miembro) {
                $template->setValue("n-g#" . ($j + 1), ($j + 1));
                $template->setValue("interesado#" . ($j + 1), $miembro["Interesado"]);
                $template->setValue("requerimiento#" . ($j + 1), $miembro["Requerimiento"]);
                $template->setValue("tipo#" . ($j + 1), $miembro["Tipo"]);
            }

            $template->saveAs($tempfiledocx);
            $headers = [
                'Content-Type' => 'application/msword',
                'Content-Disposition' => 'attachment;filename="contexto.docx"',
            ];
            return response()->download($tempfiledocx, 'contexto.docx', $headers);
        } else {
            return response([
                "message" => "!No cuenta con ningún contexto en este periodo",
            ], 404);
        }
    }

    public function reportAnual(Request $request)
    {
        //Rango de periodos
        $startYear = $request->input('startYear');
        $startSemester = $request->input('startSemester');
        $endYear = $request->input('endYear');
        $endSemester = $request->input('endSemester');
        $dates = DateModel::where(function ($query) use ($startYear, $startSemester, $endYear, $endSemester) {
            $query->where(function ($query) use ($startYear, $startSemester) {
                $query->where('year', '>', $startYear)
                    ->orWhere(function ($query) use ($startYear, $startSemester) {
                        $query->where('year', $startYear)
                            ->where('semester', '>=', $startSemester);
                    });
            })
                ->where(function ($query) use ($endYear, $endSemester) {
                    $query->where('year', '<', $endYear)
                        ->orWhere(function ($query) use ($endYear, $endSemester) {
                            $query->where('year', $endYear)
                                ->where('semester', '<=', $endSemester);
                        });
                });
        })->get();
        try {
            $spreadsheet = IOFactory::load('Reporte-Anual-Plantilla.xlsx');
            $cant_fil = $dates->count() - 1;
            $hoja = $spreadsheet->getActiveSheet();
            //Definimos el ancho de las celdas
            foreach (range('A', 'G') as $columna) {
                $hoja->getColumnDimension($columna)->setWidth(15);
            }
            //Insertamos las filas correspondientes a cada periodo         
            $hoja->insertNewRowBefore(5, $cant_fil);
            $hoja->insertNewRowBefore(10 + $cant_fil, $cant_fil);

            $filaInicio = 13 + $cant_fil * 2;
            $filaFin = 39 + $cant_fil * 2;
            $columnaFuente = 'E';

            // Calcula las columnas de destino
            $columnasDestino = [];
            for ($i = 0; $i < $cant_fil; $i++) {
                $columnasDestino[] = chr(ord($columnaFuente) + $i + 1);
            }

            // Copia el estilo a las columnas de destino
            for ($fila = $filaInicio; $fila <= $filaFin; $fila++) {
                $celdaFuente = $columnaFuente . $fila;
                $estilo = $hoja->getStyle($celdaFuente);

                foreach ($columnasDestino as $columnaDestino) {
                    $celdaDestino = $columnaDestino . $fila;
                    $hoja->duplicateStyle($estilo, $celdaDestino);
                }
            }
            //Variables para la suma de niveles
            $filaI = $filaInicio + 3;
            $filaF = $filaI + 6;

            foreach ($dates as $k => $date) {
                $faculty = FacultyStaffModel::where("date_id", $date->id)->first();
                $fila1Act = $k + 4;
                $hoja->setCellValue('A' . $fila1Act, $date->year . "-" . $date->semester);
                $hoja->setCellValue('B' . $fila1Act, $faculty->number_extraordinary_professor);
                $hoja->setCellValue('C' . $fila1Act, $faculty->number_ordinary_professor_main);
                $hoja->setCellValue('D' . $fila1Act, $faculty->number_ordinary_professor_associate);
                $hoja->setCellValue('E' . $fila1Act, $faculty->number_ordinary_professor_assistant);

                //2da tabla
                $filaActual = $k + 9 + $cant_fil;
                $hoja->setCellValue('A' . $filaActual, $date->year . "-" . $date->semester);
                $hoja->setCellValue('B' . $filaActual, $faculty->ordinary_professor_exclusive_dedication);
                $hoja->setCellValue('C' . $filaActual, $faculty->ordinary_professor_fulltime);
                $hoja->setCellValue('D' . $filaActual, $faculty->ordinary_professor_parttime);
                $hoja->setCellValue('E' . $filaActual, $faculty->contractor_professor_fulltime);
                $hoja->setCellValue('F' . $filaActual, $faculty->contractor_professor_parttime);
                $hoja->setCellValue('G' . $filaActual, "=SUM(B$filaActual:F$filaActual)");
                $hoja->setCellValue('F' . $fila1Act, "=SUM(E$filaActual:F$filaActual)");
                $hoja->setCellValue('G' . $fila1Act, "=SUM(B$fila1Act:F$fila1Act)");
                //
                //3era tabla
                $col = chr(ord($columnaFuente) + $k);
                $hoja->setCellValue($col . $filaInicio, $date->year . "-" . $date->semester);
                $hoja->setCellValue($col . $filaInicio + 1, "=SUM($col$filaI:$col$filaF)");
                $hoja->setCellValue($col . $filaInicio + 2, $faculty->distinguished_researcher);
                $hoja->setCellValue($col . $filaInicio + 3, $faculty->researcher_level_i);
                $hoja->setCellValue($col . $filaInicio + 4, $faculty->researcher_level_ii);
                $hoja->setCellValue($col . $filaInicio + 5, $faculty->researcher_level_iii);
                $hoja->setCellValue($col . $filaInicio + 6, $faculty->researcher_level_vi);
                $hoja->setCellValue($col . $filaInicio + 7, $faculty->researcher_level_v);
                $hoja->setCellValue($col . $filaInicio + 8, $faculty->researcher_level_vi);
                $hoja->setCellValue($col . $filaInicio + 9, $faculty->researcher_level_vii);
                //space
                $hoja->setCellValue($col . $filaInicio + 11, $faculty->number_publications_indexed);
                $hoja->setCellValue($col . $filaInicio + 12, $faculty->intellectual_property_indecopi);
                $hoja->setCellValue($col . $filaInicio + 13, $faculty->number_research_project_inexecution);
                $hoja->setCellValue($col . $filaInicio + 14, $faculty->number_research_project_completed);
                $hoja->setCellValue($col . $filaInicio + 15, $faculty->number_professor_inperson_academic_movility);
                $hoja->setCellValue($col . $filaInicio + 16, $faculty->number_professor_virtual_academic_movility);
                //Ultima tabla
                $hoja->setCellValue($col . $filaInicio + 19, $date->year . "-" . $date->semester);
                $hoja->setCellValue($col . $filaInicio + 20, $faculty->number_vacancies);
                $hoja->setCellValue($col . $filaInicio + 21, $faculty->number_applicants);
                $hoja->setCellValue($col . $filaInicio + 22, $faculty->number_admitted_candidates);
                $hoja->setCellValue($col . $filaInicio + 23, $faculty->number_enrolled_students);
                $hoja->setCellValue($col . $filaInicio + 24, $faculty->number_graduates);
                $hoja->setCellValue($col . $filaInicio + 25, $faculty->number_alumni);
                $hoja->setCellValue($col . $filaInicio + 26, $faculty->number_degree_recipients);
            }

            $rutaTemporal = tempnam(sys_get_temp_dir(), 'excel');
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($rutaTemporal);

            return response()->download($rutaTemporal, "reporte_anual{$startYear}-{$startSemester}_{$endYear}-{$endSemester}.xlsx")->deleteFileAfterSend(true);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            die('Error al cargar el archivo: ' . $e->getMessage());
        }
    }
}
