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
            'code' => "OM01-01-2023",
            'name' => "plancito prueba",
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
