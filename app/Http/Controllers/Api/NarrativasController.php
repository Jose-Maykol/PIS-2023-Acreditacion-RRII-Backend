<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DateModel;
use App\Repositories\UserRepository;
use App\Models\StandardModel;
use App\Models\NarrativeModel;
use App\Models\RegistrationStatusModel;
use App\Models\User;
use App\Repositories\StandardRepository;
use Illuminate\Support\Facades\Validator;



class NarrativasController extends Controller
{
    protected $standardRepository;
    public function __construct(StandardRepository $standardRepository)
    {
        $this->standardRepository = $standardRepository;
    }
    /* public function create($year, $semester, $standard_id, Request $request)
    {
        
            ruta(post): /api/standard/{standard}/narratives
            ruta(post): /api/standard/1/narratives
            datos:
            {
                "standard_id":"1",
                "content":"Narrativa de prueba"
            } 
        
        $user = auth()->user();
        if ($user->isAdmin() or $user->isAssignStandard($standard_id)) {
            $validator = Validator::make($request->all(), [
                "standard_id" => "required|integer|exists:standards,id",
                "content" => "required",
            ]);

            if ($validator->fails()) {
                return response([
                    "message" => $validator->errors()
                ], 500); //el servidor no pudo interpretar la solicitud dada una sintaxis inválida.
            }

            $standard = StandardModel::find($standard_id);
            $standard->content = $request->content;
            $standard->save();
            return response([
                "message" => "!Narrativa creada exitosamente",
                "data" => $standard,
            ], 201); //Recurso creado
        } else {
            return response([
                "message" => "No tiene permisos para crear una narrativa",
                "data" => null,
            ], 403); //Sin permisos
        }
    } */

    public function update($year, $semester, $standard_id, Request $request)
    {
        /*
            ruta(put): /api/standards/{standard_id}/narratives/
            ruta(put): /api/2023/A/standards/1/narratives/
            datos:
            {
                "id":"1",
                "narrative":"Update Narrativa"
            }
        */
        $request->validate([
            "narrative" => "required",
        ]);
        if (StandardModel::where("id", $standard_id)->exists()) {
            $standard = StandardModel::find($standard_id);
            $standard->update([
                "narrative" => $request->narrative,
            ]);
            return response([
                "status" => 1,
                "message" => "Narrativa actualizada",
            ], 200);
        } else {
            return response([
                "status" => 0,
                "message" => "No se encontro la narrativa",
            ], 404);
        }
    }

    public function delete($year, $semester, $standard_id)
    {
        /*
            ruta(delete): /api/standard/{standard}/narratives/{narrative}
            ruta(delete): /api/standard/1/narratives/2
            datos: {json con los datos qué nos mandan}
        */
        if (StandardModel::where("id", $standard_id)->exists()) {
            $standard = StandardModel::find($standard_id);
            $standard->update([
                "narrative" => ""
            ]);
            return response([
                "status" => 1,
                "message" => "Narrativa eliminada",
            ]);
        } else {
            return response([
                "status" => 0,
                "message" => "No se encontro la narrativa",
            ], 404);
        }
    }

    public function get($year, $semester, $standard_id)
    {
        /*
            ruta(get): /api/standards/{standard_id}/narratives/{narrative_id}
            ruta(get): /api/standards/1/narratives/1
            datos: {json con los datos qué nos mandan}
        */
        
        if (StandardModel::where("id", $standard_id)->exists()) {
            $standard = StandardModel::where("id", $standard_id)->select("id", "narrative")->first();
            $standard->isManager = auth()->user()->isAssignStandard($standard_id);
            $standard->isAdministrator = auth()->user()->isAdmin();
            if ($this->standardRepository->isBeingEdited($standard_id)) {
                $standard->isBlock = true;
                $user = $this->standardRepository->getUserBlockNarrative($standard_id);
                if ($user->providers()->first() !== null) {
                    $user->avatar = $user->providers()->first()->avatar;
                } else {
                    $user->avatar = null;
                }
                $standard->block_user = [
                    'user_name' => $user->lastname . ' ' . $user->name,
                    'user_email' => $user->email,
                    'user_avatar' => $user->avatar
                ];
            }
            else {
                $standard->isBlock = false;
            }
            return response()->json([
                "status" => 1,
                "data" => $standard,
            ], 200);
        } else {
            return response([
                "message" => "No se encontro la narrativa",
            ], 404);
        }
    }
/*
    public function listNarratives()
    {
        
            ruta(get): /api/standard/{standard}/narratives
            ruta(get): /api/standard/1/narratives
            datos: {json con los datos qué nos mandan}
        
        $narratives = NarrativeModel::all();
        return response([
            "message" => "!Lista de narrativas",
            "data" => $narratives,
        ], 200);
    }

    public function lastNarrative($year, $semester, $standard_id, $narrative_id)
    {
        
            ruta(get): /api/standard/{standard}/narratives/last
            ruta(get): /api/standard/1/narratives/last/1
            datos: {json con los datos qué nos mandan}
        
        $request->validate([
            "standard_id" => 'required|exists:App\Models\Standard,id',
        ]);
        $narrative = NarrativeModel::where("standard_id", $standard_id)->latest()->first();
        return response([
            "message" => "!Ultima Narrativa del estandar " . $standard_id,
            "data" => $narrative,
        ], 200);
    }*/

    // api/narratives/export
    public function reportAll(Request $request)
    {
        $tempfiledocx = tempnam(sys_get_temp_dir(), 'PHPWord');
        $template = new \PhpOffice\PhpWord\TemplateProcessor('plantilla-narrativa-v3.docx');
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
                    else{
                        $template->setValue('narrativa#' . ($j + 1) . '#'.($key + 1) , "Este periodo no tiene ningún estándar o narrativa");
                    }
                } 
            }

            $template->saveAs($tempfiledocx);
            $headers = [
                'Content-Type' => 'application/msword',
                'Content-Disposition' => 'attachment;filename="narrativas.docx"',
            ];
            return response()->download($tempfiledocx, "reporte-narrativas {$startYear}-{$startSemester}_{$endYear}-{$endSemester}.docx", $headers);
        } else{
            return response([
                "message" => "!No cuenta con ningún estándar todavía en este periodo",
            ], 404);
        }
    } 
}
