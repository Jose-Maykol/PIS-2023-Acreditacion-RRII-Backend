<?php

namespace Database\Seeders;

use App\Models\RegistrationStatusModel;
use App\Models\DateModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('plans')->insert([
            'code' => "OM01-06-2023",
            'name' => "plancito prueba 6",
            'opportunity_for_improvement' => "esta es una prueba para crear un plancito y ver y probar el export",
            'semester_execution' => 'Abeceda',
            'advance' => 15, 
            'duration' => 4, //meses
            'efficacy_evaluation' => True,
            'plan_status_id' => 3,
            'standard_id' => 1,
            'user_id' => 4,
            'date_id' => DateModel::dateId(2023,'A'),
            'registration_status_id' => RegistrationStatusModel::registrationActiveId(),
        ]);
        DB::table('plans')->insert([
            'code' => "OM01-02-2023",
            'name' => "este es un 2do plan",
            'opportunity_for_improvement' => "esta es una prueba para crear un plancito y ver y probar el export",
            'semester_execution' => 'Abeceda',
            'advance' => 12, 
            'duration' => 4, //meses
            'efficacy_evaluation' => True,
            'plan_status_id' => 1,
            'standard_id' => 1,
            'user_id' => 4,
            'date_id' => DateModel::dateId(2023,'A'),
            'registration_status_id' => RegistrationStatusModel::registrationActiveId(),
        ]);
        DB::table('plans')->insert([
            'code' => "OM01-03-2023",
            'name' => "este es un 3er plan",
            'opportunity_for_improvement' => "esta es una prueba para crear un plancito y ver y probar el export",
            'semester_execution' => 'Abeceda',
            'advance' => 15, 
            'duration' => 4, //meses
            'efficacy_evaluation' => True,
            'plan_status_id' => 3,
            'standard_id' => 3,
            'user_id' => 4,
            'date_id' => DateModel::dateId(2023,'A'),
            'registration_status_id' => RegistrationStatusModel::registrationActiveId(),
        ]);
        DB::table('plans')->insert([
            'code' => "OM01-04-2023",
            'name' => "este es un 4to plan",
            'opportunity_for_improvement' => "esta es una prueba para crear un plancito y ver y probar el export",
            'semester_execution' => 'Abeceda',
            'advance' => 15, 
            'duration' => 4, //meses
            'efficacy_evaluation' => True,
            'plan_status_id' => 4,
            'standard_id' => 2,
            'user_id' => 4,
            'date_id' => DateModel::dateId(2023,'A'),
            'registration_status_id' => RegistrationStatusModel::registrationActiveId(),
        ]);
        DB::table('plans')->insert([
            'code' => "OM01-05-2023",
            'name' => "este un 5to plan",
            'opportunity_for_improvement' => "esta es una prueba para crear un plancito y ver y probar el export",
            'semester_execution' => 'Abeceda',
            'advance' => 15, 
            'duration' => 4, //meses
            'efficacy_evaluation' => True,
            'plan_status_id' => 2,
            'standard_id' => 1,
            'user_id' => 4,
            'date_id' => DateModel::dateId(2023,'A'),
            'registration_status_id' => RegistrationStatusModel::registrationActiveId(),
        ]);
    }
}
