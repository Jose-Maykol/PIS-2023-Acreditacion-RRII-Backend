<?php

namespace App\Services;

use App\Models\StandardModel;

use App\Repositories\DateSemesterRepository;

use Illuminate\Http\Request;

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
        $template = new \PhpOffice\PhpWord\TemplateProcessor('plantilla-narrativa-v3.docx');
        
        //Rango de periodos
        $startYear = $request->input('startYear');
        $startSemester = $request->input('startSemester');
        $endYear = $request->input('endYear');
        $endSemester = $request->input('endSemester');
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
                        $template->setValue('narrativa#' . ($j + 1) . '#'.($key + 1) , $estandar->narrative);
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
            return response()->download($tempfiledocx, "reporte_narrativas_{$startYear}-{$startSemester}_{$endYear}-{$endSemester}.docx", $headers);
        } else{
            return response([
                "message" => "!No cuenta con ningún estándar todavía en este periodo",
            ], 404);
        }
    } 
}