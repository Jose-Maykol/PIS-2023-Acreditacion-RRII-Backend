<?php

namespace App\Repositories;

use App\Models\Evidence;
use App\Models\Folder;
use Illuminate\Support\Facades\DB;
use App\Models\DateModel;
use App\Models\RegistrationStatusModel;

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
}
