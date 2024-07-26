<?php

namespace App\Services;

use App\Models\StandardModel;

use App\Repositories\DateSemesterRepository;

use Illuminate\Http\Request;
use App\Services\GoogleDriveService;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\TemplateProcessor;

class NarrativeService
{

    protected $googleDriveService;
    protected $dateRepository;

    public function __construct(
        DateSemesterRepository $dateRepository,
        GoogleDriveService $googleDriveService
    ) {

        $this->dateRepository = $dateRepository;
        $this->googleDriveService = new $googleDriveService;
    }

    public function reportAllNarratives(Request $request)
    {
        $tempfiledocx = tempnam(sys_get_temp_dir(), 'PHPWord');
        $template = new TemplateProcessor('plantilla-narrativa-v3.docx');

        // Rango de periodos
        $startYear = $request->input('startYear');
        $startSemester = $request->input('startSemester');
        $endYear = $request->input('endYear');
        $endSemester = $request->input('endSemester');

        // Comprobaciones
        if ($startYear > $endYear) {
            $temp = $startYear;
            $startYear = $endYear;
            $endYear = $temp;

            $tempSemester = $startSemester;
            $startSemester  = $endSemester;
            $endSemester = $tempSemester;
        } else if ($startYear == $endYear && $startSemester == 'B') {
            $tempSemester = $startSemester;
            $startSemester  = $endSemester;
            $endSemester = $tempSemester;
        }

        $dates = $this->dateRepository->getDatesByRange($startYear, $startSemester, $endYear, $endSemester);
        $standards = StandardModel::where("date_id", 1)->orderBy('nro_standard')->get();

        if ($standards->count() > 0) {
            $template->cloneBlock('block_periodo', $dates->count(), true, true);
            $template->cloneBlock('block_estandar', $standards->count(), true, true);

            foreach ($standards as $key => $standard) {
                $template->setValue('dimension#' . ($key + 1), $standard->dimension);
                $template->setValue('factor#' . ($key + 1), $standard->factor);
                $template->setValue('n#' . ($key + 1), $standard->nro_standard);
                $template->setValue('estandar#' . ($key + 1), $standard->name);

                foreach ($dates as $j => $date) {
                    $template->setValue('year#' . ($j + 1) . '#' . ($key + 1), $date->year);
                    $template->setValue('semester#' . ($j + 1) . '#' . ($key + 1), $date->semester);
                    $estandar = StandardModel::where("nro_standard", $standard->nro_standard)->where("date_id", $date->id)->first();

                    if ($estandar != null && $estandar->document_id != null) {
                        Log::info("Procesando documento para estándar: {$standard->nro_standard}, fecha: {$date->year}-{$date->semester}");
                        $tempFile = $this->googleDriveService->downloadAsWord($estandar->document_id);
                        $documentContent = IOFactory::load($tempFile);

                        $sections = $documentContent->getSections();

                        foreach ($sections as $section) {
                            foreach ($section->getElements() as $element) {
                                $template->setComplexValue('narrativa#' . ($j + 1) . '#' . ($key + 1), $element);
                            }
                        }

                        unlink($tempFile);
                    } else if ($estandar == null) {
                        $template->setValue('narrativa#' . ($j + 1) . '#' . ($key + 1), "Este periodo no tiene ningún estándar");
                    } else if ($estandar->document_id == null) {
                        $template->setValue('narrativa#' . ($j + 1) . '#' . ($key + 1), "No cuenta con narrativa");
                    }
                }
            }

            $template->saveAs($tempfiledocx);

            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment;filename="narrativas.docx"',
            ];

            if ($dates->count() > 1) {
                return response()->download($tempfiledocx, "reporte_narrativas_{$startYear}-{$startSemester}_{$endYear}-{$endSemester}.docx", $headers);
            }

            return response()->download($tempfiledocx, "reporte_narrativas_{$startYear}-{$startSemester}.docx", $headers);
        } else {
            return response([
                "message" => "!No cuenta con ningún estándar todavía en este periodo",
            ], 404);
        }
    }
}
