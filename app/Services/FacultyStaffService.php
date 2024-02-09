<?php

namespace App\Services;

use App\Repositories\DateSemesterRepository;
use App\Repositories\FacultyStaffRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
//
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FacultyStaffService
{

    protected $userRepository;
    protected $dateSemesterRepository;
    protected $facultyStaffRepository;
    public function __construct(FacultyStaffRepository $facultyStaffRepository, UserRepository $userRepository, DateSemesterRepository $dateSemesterRepository)
    {
        $this->facultyStaffRepository = $facultyStaffRepository;
        $this->dateSemesterRepository = $dateSemesterRepository;
        $this->userRepository = $userRepository;
    }
    public function createFacultyStaff($year, $semester, $data){
        $userAuth = auth()->user();
        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        
        if (!$this->dateSemesterRepository->dateSemesterExists2($year, $semester)) {
            throw new \App\Exceptions\DateSemester\DateSemesterNotFoundException();
        }
        $id_date_semester = $this->dateSemesterRepository->dateId($year, $semester);
        $data['date_id'] = $id_date_semester;
        $facultyStaff = $this->facultyStaffRepository->createFacultyStaff($data);
        return $facultyStaff;
    }
    public function updateFacultyStaff($year, $semester, $data){
        $userAuth = auth()->user();
        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        
        if (!$this->dateSemesterRepository->dateSemesterExists2($year, $semester)) {
            throw new \App\Exceptions\DateSemester\DateSemesterNotFoundException();
        }
        $date_id = $this->dateSemesterRepository->dateId($year, $semester);
        $facultyStaff = $this->facultyStaffRepository->updateFacultyStaff($date_id, $data);
        return $facultyStaff;
    }
    public function getFacultyStaff($year, $semester){
        $userAuth = auth()->user();
        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        
        if (!$this->dateSemesterRepository->dateSemesterExists2($year, $semester)) {
            throw new \App\Exceptions\DateSemester\DateSemesterNotFoundException();
        }
        $id_date_semester = $this->dateSemesterRepository->dateId($year, $semester);
        $facultyStaff = $this->facultyStaffRepository->getFacultyStaff($id_date_semester);
        return $facultyStaff;
    }

    public function reportAnual(Request $request)
    {
        $spreadsheet = IOFactory::load('Reporte-Anual-Plantilla.xlsx');
        $hoja = $spreadsheet->getActiveSheet();
        //Definimos el ancho de las celdas
        foreach (range('A', 'G') as $columna) {
            $hoja->getColumnDimension($columna)->setWidth(15);
        }
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
        if($startYear == $endYear && $startSemester == $endSemester){
            $dates = $this->dateSemesterRepository->getDate($startYear, $startSemester);
        }else{
            $dates = $this->dateSemesterRepository->getDatesByRange($startYear, $startSemester, $endYear, $endSemester);
        }
        try {
            $cant_fil = $dates->count() - 1;
            if($dates->count()>1){
                //Insertamos las filas correspondientes a cada periodo         
                $hoja->insertNewRowBefore(5, $cant_fil);
                $hoja->insertNewRowBefore(10 + $cant_fil, $cant_fil);
            }
            
            $filaInicio = 13 + $cant_fil * 2;
            $filaFin = 39 + $cant_fil * 2;
            $columnaFuente = 'E';

            if($dates->count()>1){
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
            }
            //Variables para la suma de niveles
            $filaI = $filaInicio + 3;
            $filaF = $filaI + 6;
            
            foreach ($dates as $k => $date) {
                $faculty = $this->facultyStaffRepository->getFacultyStaff($date->id)->first();
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
            if($dates->count()>1){
                return response()->download($rutaTemporal, "reporte_anual_{$startYear}-{$startSemester}_{$endYear}-{$endSemester}.xlsx")->deleteFileAfterSend(true);
            }
            return response()->download($rutaTemporal, "reporte_anual_{$startYear}-{$startSemester}.xlsx")->deleteFileAfterSend(true);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            die('Error al cargar el archivo: ' . $e->getMessage());
        }
    }
}