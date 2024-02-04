<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function planStatistics($year, $semester)
    {
        $planStatistics =  DB::table('plan_status')
        ->leftJoin('plans', function($join) use ($year, $semester) {
            $join->on('plan_status.id', '=', 'plans.plan_status_id')
                ->join('date_semesters', function($join) use ($year, $semester) {
                    $join->on('plans.date_id', '=', 'date_semesters.id')
                        ->where('date_semesters.year', $year)
                        ->where('date_semesters.semester', $semester);
                });
        })
        ->select(DB::raw('initcap(plan_status.description) as label'), DB::raw('COALESCE(COUNT(plans.id), 0) as value'))
        ->groupBy('plan_status.description')
        ->get();

        return response([
            "status" => 1,
            "message" => "Estadísticas de planes",
            "data" => $planStatistics
        ], 200);
    }

    public function planPerStandardStatistics($year, $semester)
    {
        $planPerStandardStatistics =  DB::table('plans')
        ->join('plan_status', 'plan_status.id', '=', 'plans.plan_status_id')
        ->join('date_semesters', function ($joinDates) use ($year, $semester) {
            $joinDates->on('plans.date_id', '=', 'date_semesters.id')
                ->where('date_semesters.year', $year)
                ->where('date_semesters.semester', $semester);
        })
        ->join('standards', 'plans.standard_id', '=', 'standards.id')
        ->select('standards.id as standard_id', 'standards.name as standard_name')
        ->selectRaw('COUNT(plans.id) as total_plans')
        ->groupBy('standards.id', 'standards.name')
        ->orderBy('standards.nro_standard')
        ->get();

        return response([
            "status" => 1,
            "message" => "Estadísticas de planes por estándar",
            "data" => $planPerStandardStatistics
        ], 200);
    }
}