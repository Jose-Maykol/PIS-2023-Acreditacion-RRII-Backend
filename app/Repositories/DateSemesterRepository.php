<?php

namespace App\Repositories;

use App\Models\Evidence;
use App\Models\Folder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\DateModel;
use App\Models\FacultyStaffModel;
use App\Models\IdentificationContextModel;
use App\Models\RegistrationStatusModel;
use App\Models\StandardModel;

class DateSemesterRepository
{
    public function listDateSemester()
    {
        return DateModel::where('registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->select('year', 'semester')
            ->get()
            ->groupBy('year')->map(function ($items, $year) {
                return [
                    'year' => $year,
                    'semester' => $items->pluck('semester')->toArray()
                ];
            });
    }
    public function createDateSemester($year, $semester)
    {
        $date_semester = new DateModel();
        $date_semester->year = $year;
        $date_semester->semester = $semester;
        $date_semester->registration_status_id = RegistrationStatusModel::registrationActiveId();
        $date_semester->save();
        return $date_semester;
    }
    public function statusDateSemester($year, $semester)
    {
        $date_id = DateModel::dateId($year, $semester);
        $standardsExists = StandardModel::where('date_id', $date_id)->exists();
        $identificationContextExists = IdentificationContextModel::where('date_id', $date_id)->exists();
        $facultyStaffExists = FacultyStaffModel::where('date_id', $date_id)->exists();
        $result = [
            'standards' => $standardsExists == true ? 'completado' : 'faltante',
            'identification_context' => $identificationContextExists == true ? 'completado' : 'faltante',
            'faculty_staff' => $facultyStaffExists == true ? 'completado' : 'faltante'
        ];
        return $result;
    }
    public function readDateSemester($id_date_semester)
    {
        $date_semester = DateModel::where('id', $id_date_semester)
            ->where('registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->get();

        return $date_semester;
    }

    public function updateDateSemester($id_date_semester, $year, $semester)
    {
        $date_semester = DateModel::find($id_date_semester);
        $date_semester->year = $year;
        $date_semester->semester = $semester;
        $date_semester->save();
        return $date_semester;
    }

    public function closeDateSemester($id_date_semester, $closing_date)
    {
        $date_semester = DateModel::where('id', $id_date_semester)->first();
        $date_semester->closing_date = $closing_date;
        $date_semester->save();
        return $date_semester;
    }
    public function checkIfDateSemesterExists($year, $semester)
    {
        return DateModel::where('year', $year)
            ->where('semester', $semester)
            ->where('registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->exists();
    }
    public function dateSemesterExists($id_date_semester)
    {
        return DateModel::where('id', $id_date_semester)
            ->where('registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->exists();
    }
    public function dateSemesterExists2($year, $semester)
    {
        return DateModel::where('year', $year)
            ->where('semester', $semester)
            ->where('registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->exists();
    }
    public function dateId($year, $semester){
        return DateModel::where('year', $year)->where('semester', $semester)->value('id');
    }

    public function getDatesByRange($startYear, $startSemester, $endYear, $endSemester){

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

        return $dates;
    }
}
