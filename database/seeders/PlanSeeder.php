<?php

namespace Database\Seeders;

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
            'code' => 'OM.01-01',
            'name' => 'Renombrando algo',
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd',
            'semester_execution' => '2023-A',
            'advance' => 50,
            'duration' => 3,
            'plan_status_id' => 3,
            'efficacy_evaluation' => 1,
            'standard_id' => 1,
            'user_id' => 1,
            'date_id' => 5,
            'registration_status_id' => 1,
        ]);
    }
}
