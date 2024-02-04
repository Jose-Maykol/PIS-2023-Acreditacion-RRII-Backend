<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EvidenceRequest;
use App\Models\DateModel;
use App\Models\EvidenceModel;
use App\Models\EvidenceTypeModel;
use App\Models\StandardModel;
use App\Services\EvidenceService;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use ZipArchive;


class EvidencesController extends Controller
{
    protected $evidenceService;

    public function __construct(EvidenceService $evidenceService)
    {

        $this->evidenceService = $evidenceService;
    }
    public function createEvidence(EvidenceRequest $request)
    {
    }



    public function show($id)
    {
        $result = $this->evidenceService->showEvidence($id);
        return response([
            "status" => 1,
            "message" => "Evidencia",
            "data" => $result,
        ]);
    }
    public function createFileEvidence(EvidenceRequest $request)
    {
        try {
            $request->validated();
            $result = $this->evidenceService->createFileEvidence($request);
            return response([
                "status" => 1,
                "message" => "Evidencia creada con existo",
                "data" => $result,
            ]);
        } catch (\App\Exceptions\Evidence\EvidenceAlreadyExistsException $e) {
            return response([
                "status" => 1,
                "message" => "Ya existe este archivo.",
                "data" => [
                    "file" => $request->file('file')->getClientOriginalName(),
                    "is_upload" => false
                ]
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function viewFile($year, $semester, $file_id)
    {
        try {
            $result = $this->evidenceService->viewFile($year, $semester, $file_id);
            $contentType = $this->getContentType($result['extension']);
            $result['type'] = $contentType;

            return response([
                "status" => 1,
                "message" => "Archivo obtenido con éxito.",
                "data" => $result
            ], 200)->header('Content-Type', 'application/json');
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function viewEvidence($year, $semester, $evidence_id)
    {
        try {
            $result = $this->evidenceService->viewEvidence($year, $semester, $evidence_id);
            if (isset($result['extension'])) {
                $contentType = $this->getContentType($result['extension']);
                $result['type'] = $contentType;
            }

            return response([
                "status" => 1,
                "message" => (isset($result['extension'])) ? "Archivo obtenido con éxito." : "Archivos obtenidos con éxito",
                "data" => $result
            ], 200)->header('Content-Type', 'application/json');
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function downloadFile($year, $semester, $file_id)
    {
        try {
            $result = $this->evidenceService->downloadFile($year, $semester, $file_id);
            $headers = ['Content-Type' => 'application/pdf'];
            return response()->download($result['path'], $result['name'], $headers);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function renameFile(EvidenceRequest $request, $year, $semester, $file_id)
    {
        try {
            $request->validated();
            $result = $this->evidenceService->renameFile($request);
            return response([
                "status" => 1,
                "message" => "Nombre de evidencia actualizada exitosamente",
                "data" => $result
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function moveFile(EvidenceRequest $request, $year, $semester, $file_id)
    {
        try {
            $request->validated();
            $result = $this->evidenceService->moveFile($request);
            return response([
                "status" => 1,
                "message" => "Archivo movido exitosamente",
                "data" => $result
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function deleteFile($year, $semester, $file_id)
    {
        try {
            $result = $this->evidenceService->deleteFile($year, $semester, $file_id);
            return response([
                "status" => 1,
                "message" => "Archivo eliminado exitosamente",
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $year, $semester, $evidence_id)
    {
        $request->validate([
            "file" => "file",
        ]);

        if (Evidence::where(['id' => $evidence_id])->exists()) {
            $file = $request->file('file');
            $id_user = auth()->user();
            $evidence = Evidence::find($evidence_id);

            if ($evidence->file !== $file->getClientOriginalName()) {
                return response([
                    "status" => 0,
                    "message" => "La evidencia a actualizar no tiene el mismo nombre de archivo",
                ], 404);
            }

            $standardId = $evidence->standard_id;
            $typeEvidenceId = $evidence->type_evidence_id;
            $pathEvidence = $evidence->path;
            $pathEvidence = preg_replace('/^\/+/', '', $pathEvidence);
            $path = $file->storeAs('evidencias/' . $year . '/' . $semester . '/' . 'estandar_' . $standardId . '/tipo_evidencia_' . $typeEvidenceId . '/' . $pathEvidence, $file->getClientOriginalName());
            $evidence->type = $file->getClientOriginalExtension();
            $evidence->size = $file->getSize();
            $evidence->save();
            return response([
                "status" => 1,
                "message" => "Evidencia actualizada exitosamente",
            ], 200);
        } else {
            return response([
                "status" => 0,
                "message" => "No se encontro la evidencia",
            ], 404);
        }
    }

    private function getContentType($extension)
    {
        $contentTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
        ];
        if (isset($contentTypes[$extension])) {
            return $contentTypes[$extension];
        }
        return 'application/octet-stream';
    }

    public function reportAllEvidences(Request $request)
    {
        $tempfiledocx = tempnam(sys_get_temp_dir(), 'PHPWord');
        $template = new \PhpOffice\PhpWord\TemplateProcessor('plantilla-evidencias.docx');
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
        $standards = StandardModel::where("date_id", 1)->orderBy('nro_standard')->get();
        if ($standards->count() > 0) {
            $template->cloneBlock('block_periodo', $dates->count(), true, true);
            $template->cloneBlock('block_estandar', $standards->count(), true, true);

            foreach ($standards as $key => $standard) {
                // Dimensión
                $template->setValue('dimension#' . ($key + 1), $standard->dimension);
                // Factor
                $template->setValue('factor#' . ($key + 1), $standard->factor);
                // Estandar
                $template->setValue('n-e#' . ($key + 1), $standard->nro_standard);
                $template->setValue('estandar#' . ($key + 1), $standard->name);


                //Periodos
                foreach ($dates as $j => $date) {
                    $template->setValue('year#' . ($j + 1) . '#' . ($key + 1), $date->year);
                    $template->setValue('semester#' . ($j + 1) . '#' . ($key + 1), $date->semester);
                    $evidencesCount = EvidenceModel::where("standard_id", $standard->id)->where("date_id", $date->id)->count();
                    if ($evidencesCount > 0) {
                        $template->cloneRow('n#' . ($j + 1) . '#' . ($key + 1), $evidencesCount);
                        $evidencias = EvidenceModel::where("standard_id", $standard->id)->where("date_id", $date->id)->get();
                        foreach ($evidencias as $m => $evidence) {
                            $template->setValue('n#' . ($j + 1) . "#" . ($key + 1) . "#" . ($m + 1), ($m + 1));
                            $template->setValue('codigo#' . ($j + 1) . "#" . ($key + 1) . "#" . ($m + 1), "No data");
                            $template->setValue('nombre#' . ($j + 1) . "#" . ($key + 1) . "#" . ($m + 1), $evidence->name);
                            $template->setValue('tipo#' . ($j + 1) . "#" . ($key + 1) . "#" . ($m + 1), EvidenceTypeModel::evidenceId($evidence->evidence_type_id));
                            $template->setValue('tamaño#' . ($j + 1) . "#" . ($key + 1) . "#" . ($m + 1), $evidence->size);
                            $template->setValue('fecha#' . ($j + 1) . "#" . ($key + 1) . "#" . ($m + 1), $evidence->created_at);
                        }
                    } else {
                        $template->cloneRow('n#' . ($j + 1) . '#' . ($key + 1), -1);
                        $template->replaceBlock("block_tabla#" . ($j + 1) . "#" . ($key + 1), "No hay evidencias");
                    }
                }
            }

            $template->saveAs($tempfiledocx);
            $headers = [
                'Content-Type' => 'application/msword',
                'Content-Disposition' => 'attachment;filename="evidencias.docx"',
            ];
            return response()->download($tempfiledocx, 'reporte-evidencias.docx', $headers);
        } else {
            return response([
                "message" => "!No cuenta con ningún estándar todavía en este periodo",
            ], 404);
        }
    }
}
