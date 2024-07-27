<?php

namespace App\Services;

use App\Models\StandardModel;

use App\Repositories\DateSemesterRepository;

use Illuminate\Http\Request;
use App\Services\GoogleDriveService;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\Element\Image;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
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
                        Log::info("Iniciando procesamiento del documento para estándar: {$standard->nro_standard}, fecha: {$date->year}-{$date->semester}");
                        $tempFile = $this->googleDriveService->downloadAsWord($estandar->document_id);
                        $documentContent = IOFactory::load($tempFile);

                        $sections = $documentContent->getSections();
                        Log::info("Número de secciones en el documento: " . count($sections));

                        $maxElements = 1000; // Ajusta este número según sea necesario

                        foreach ($sections as $sectionIndex => $section) {
                            Log::info("Procesando sección " . ($sectionIndex + 1));

                            $elements = $section->getElements();
                            Log::info("Número de elementos en la sección " . ($sectionIndex + 1) . ": " . count($elements));

                            foreach ($elements as $elementIndex => $element) {

                                Log::info("Procesando elemento " . ($elementIndex + 1) . " de tipo: " . get_class($element));

                                if ($element instanceof \PhpOffice\PhpWord\Element\Image) {
                                    Log::info("Procesando imagen");
                                    $imagePath = $element->getSource();
                                    $template->setImageValue('narrativa#' . ($j + 1) . '#' . ($key + 1), $imagePath);
                                } elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                                    Log::info("Procesando tabla");
                                    $tableData = [];
                                    foreach ($element->getRows() as $row) {
                                        $rowData = [];
                                        foreach ($row->getCells() as $cell) {
                                            $cellContent = '';
                                            foreach ($cell->getElements() as $cellElement) {
                                                if ($cellElement instanceof Text) {
                                                    $cellContent .= $cellElement->getText();
                                                } elseif ($cellElement instanceof TextRun) {
                                                    foreach ($cellElement->getElements() as $textRunElement) {
                                                        if ($textRunElement instanceof Text) {
                                                            $cellContent .= $textRunElement->getText();
                                                        }
                                                    }
                                                }
                                            }
                                            $rowData[] = $cellContent;
                                        }
                                        $tableData[] = $rowData;
                                    }

                                    if (!empty($tableData)) {
                                        $table = new Table(['unit' => \PhpOffice\PhpWord\SimpleType\TblWidth::TWIP, 'width' => 100 * 50]);
                                        foreach ($tableData as $rowData) {
                                            $tableRow = $table->addRow();
                                            foreach ($rowData as $cellContent) {
                                                $tableRow->addCell(1750)->addText($cellContent);
                                                Log::info("Contenido de celda: $cellContent");
                                            }
                                        }

                                        // Insertar la tabla en el documento
                                        $template->setComplexBlock('tabla#' . ($j + 1) . '#' . ($key + 1), $table);

                                        Log::info("Tabla creada e insertada en el template con " . count($tableData) . " filas y " . count($tableData[0]) . " columnas");
                                    } else {
                                        Log::warning("No se encontró contenido en la tabla original para crear una nueva tabla");
                                    }
                                } /* elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                                    Log::info("Procesando TextRun");
                                    $textContent = '';
                                    foreach ($element->getElements() as $textElement) {
                                        if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                                            $textContent .= $textElement->getText();
                                        }
                                    }
                                    $template->setValue('narrativa#' . ($j + 1) . '#' . ($key + 1), $textContent);
                                } elseif ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                                    Log::info("Procesando Text");
                                    $template->setValue('narrativa#' . ($j + 1) . '#' . ($key + 1), $element->getText());
                                } */ else {
                                    Log::info("Procesando otro tipo de elemento");
                                    $template->setComplexBlock('narrativa#' . ($j + 1) . '#' . ($key + 1), $element);
                                }
                            }
                        }
                        /* unlink($tempFile); */
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
