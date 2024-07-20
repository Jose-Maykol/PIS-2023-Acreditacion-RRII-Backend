<?php

namespace App\Services;

use App\Models\StandardModel;

use App\Repositories\DateSemesterRepository;

use Illuminate\Http\Request;

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\Element\TextRun;

class NarrativeService
{

    protected $dateRepository;

    public function __construct(DateSemesterRepository $dateRepository)
    {

        $this->dateRepository = $dateRepository;
    }

    public function reportAllNarratives(Request $request)
    {
        $tempfiledocx = tempnam(sys_get_temp_dir(), 'PHPWord');
        $template = new TemplateProcessor('plantilla-narrativa-v3.docx');
        
        //Rango de periodos
        $startYear = $request->input('startYear');
        $startSemester = $request->input('startSemester');
        $endYear = $request->input('endYear');
        $endSemester = $request->input('endSemester');
        //Comprobaciones
        if($startYear>$endYear){
            $temp = $startYear;
            $startYear = $endYear;
            $endYear = $temp;

            $tempSemester = $startSemester;
            $startSemester  = $endSemester;
            $endSemester = $tempSemester;
        }
        else if($startYear == $endYear && $startSemester == 'B'){
            $tempSemester = $startSemester;
            $startSemester  = $endSemester;
            $endSemester = $tempSemester;
        }
        $dates = $this->dateRepository->getDatesByRange($startYear, $startSemester, $endYear, $endSemester);

        $standards = StandardModel::where("date_id", 1)->orderBy('nro_standard')->get();
        if($standards->count() > 0){
            $template->cloneBlock('block_periodo', $dates->count(), true, true);
            $template->cloneBlock('block_estandar', $standards->count(), true, true);

            foreach ($standards as $key => $standard){
                // Dimensión
                $template->setValue('dimension#'. ($key + 1) , $standard->dimension);
                // Factor
                $template->setValue('factor#'. ($key + 1) , $standard->factor);
                // Estandar
                $template->setValue('n#'. ($key + 1) , $standard->nro_standard);
                $template->setValue('estandar#'. ($key + 1) , $standard->name);
                
                
                //Periodos
                foreach ($dates as $j => $date){
                    $template->setValue('year#' . ($j + 1) . '#'.($key + 1) , $date->year);
                    $template->setValue('semester#' . ($j + 1) . '#'.($key + 1) , $date->semester);
                    $estandar = StandardModel::where("nro_standard", $standard->nro_standard)->where("date_id", $date->id)->first();
                    if($estandar != null && $estandar->narrative != null){
                            // Parsear el HTML y convertirlo a un TextRun
                            $html = $estandar->narrative;

                            $phpWord = new PhpWord();
                            $section = $phpWord->addSection();
                            Html::addHtml($section, $html, false, false);

                            // Verificar los elementos de la sección
                            $elements = $section->getElements();
                            
                            // Crear un bloque complejo con los elementos leídos de la sección temporal
                            $textRun = new TextRun();
                            foreach ($elements as $element) {
                                if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                                    $textRun->addText($element->getText(), $element->getFontStyle(), $element->getParagraphStyle());
                                } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                                    foreach ($element->getElements() as $subElement) {
                                        $textRun->addText($subElement->getText(), $subElement->getFontStyle(), $subElement->getParagraphStyle());
                                    }
                                } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextBreak) {
                                    $textRun->addTextBreak();
                                } elseif ($element instanceof \PhpOffice\PhpWord\Element\Link) {
                                    $textRun->addLink($element->getSource(), $element->getText(), $element->getFontStyle(), $element->getParagraphStyle());
                                }
                            }
                        
                            // Insertar el TextRun en el template
                            $template->setComplexBlock('narrativa#' . ($j + 1) . '#'.($key + 1), $textRun);
                    }
                    else if($estandar == null){
                        $template->setValue('narrativa#' . ($j + 1) . '#'.($key + 1) , "Este periodo no tiene ningún estándar");
                    }
                    else if($estandar->narrative == null){
                        $template->setValue('narrativa#' . ($j + 1) . '#'.($key + 1) , "No cuenta con narrativa");
                    }
                } 
            }

            $template->saveAs($tempfiledocx);
            $headers = [
                'Content-Type' => 'application/msword',
                'Content-Disposition' => 'attachment;filename="narrativas.docx"',
            ];
            if($dates->count()>1){
                return response()->download($tempfiledocx, "reporte_narrativas_{$startYear}-{$startSemester}_{$endYear}-{$endSemester}.docx", $headers);
            }
            return response()->download($tempfiledocx, "reporte_narrativas_{$startYear}-{$startSemester}.docx", $headers);
        } else{
            return response([
                "message" => "!No cuenta con ningún estándar todavía en este periodo",
            ], 404);
        }
    } 
}