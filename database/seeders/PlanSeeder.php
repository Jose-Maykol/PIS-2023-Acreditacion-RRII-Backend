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
            'code' => 'OM01-01-2023', 
            'name' => 'Este es un plan de mejora del estandar 02 y es el 01', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2023-A', 
            'advance' => 50, 'duration' => 3, 
            'plan_status_id' => 3, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 2, 
            'user_id' => 1, 
            'date_id' => 5, 
            'registration_status_id' => 1, 
        ]); 

        DB::table('plans')->insert([ 
            'code' => 'OM02-03-2023', 
            'name' => 'Este es un plan de mejora del estandar 02 y es el 02', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2023-A', 
            'advance' => 50, 
            'duration' => 3, 
            'plan_status_id' => 1, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 2, 
            'user_id' => 1, 
            'date_id' => 5, 
            'registration_status_id' => 1, 
        ]); 

        DB::table('plans')->insert([ 
            'code' => 'OM05-04-2023', 
            'name' => 'Este es un plan de mejora del estandar 05 y es el 01', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2023-A', 
            'advance' => 50, 
            'duration' => 2, 
            'plan_status_id' => 3, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 5, 
            'user_id' => 4, 
            'date_id' => 5, 
            'registration_status_id' => 1, 
        ]); 

        DB::table('plans')->insert([ 
            'code' => 'OM05-05-2023', 
            'name' => 'Este es un plan de mejora del estandar 05 y es el 02', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2023-A', 
            'advance' => 50, 
            'duration' => 2, 
            'plan_status_id' => 3, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 5, 
            'user_id' => 4, 
            'date_id' => 5, 
            'registration_status_id' => 1, 
        ]); 

        DB::table('plans')->insert([ 
            'code' => 'OM02-06-2023', 
            'name' => 'Este es un plan de mejora del estandar 02 y sería el 03 pero está borrado', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2023-A', 
            'advance' => 50, 
            'duration' => 2, 
            'plan_status_id' => 3, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 2, 
            'user_id' => 1, 
            'date_id' => 5, 
            'registration_status_id' => 3, 
        ]); 

        DB::table('plans')->insert([ 
            'code' => 'OM05-07-2023', 
            'name' => 'Este es un plan de mejora del estandar 05 y sería el 03 pero está borrado', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2023-A', 
            'advance' => 50, 
            'duration' => 1, 
            'plan_status_id' => 3, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 5, 
            'user_id' => 4, 
            'date_id' => 5, 
            'registration_status_id' => 3, 
        ]); 

        //los del 2022 

        DB::table('plans')->insert([ 
            'code' => 'OM02-08-2022', 
            'name' => 'Este es un plan de mejora del estandar 02 y sería el 01 del año 2022 y semestre A', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2022-A', 
            'advance' => 55, 
            'duration' => 4, 
            'plan_status_id' => 2, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 2, 
            'user_id' => 6,
            'date_id' => 3, 
            'registration_status_id' => 1, 
        ]); 

        DB::table('plans')->insert([ 
            'code' => 'OM05-09-2022', 
            'name' => 'Este es un plan de mejora del estandar 05 y sería el 01 del año 2022 y semestre A', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2022-A', 
            'advance' => 58, 
            'duration' => 2, 
            'plan_status_id' => 2, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 5, 
            'user_id' => 7, 
            'date_id' => 3, 
            'registration_status_id' => 1, 
        ]); 

        DB::table('plans')->insert([ 
            'code' => 'OM05-10-2022', 
            'name' => 'Este es un plan de mejora del estandar 05 y sería el 02 del año 2022 y semestre A, pero esta borrado', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2022-A', 
            'advance' => 58, 
            'duration' => 3, 
            'plan_status_id' => 2, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 5, 
            'user_id' => 7, 
            'date_id' => 3, 
            'registration_status_id' => 3, 
        ]); 

        DB::table('plans')->insert([ 
            'code' => 'OM02-11-2022', 
            'name' => 'Este es un plan de mejora del estandar 02 y sería el 02 del año 2022 y semestre A, pero esta borrado', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2022-A', 
            'advance' => 58, 
            'duration' => 3, 
            'plan_status_id' => 2, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 2, 
            'user_id' => 6, 
            'date_id' => 3, 
            'registration_status_id' => 3, 
        ]);

        //2022 - b
        DB::table('plans')->insert([ 
            'code' => 'OM02-12-2022', 
            'name' => 'Este es un plan de mejora del estandar 02 y es el 01 del año 2022 - B', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2023-A', 
            'advance' => 50, 'duration' => 3, 
            'plan_status_id' => 3, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 2, 
            'user_id' => 3, 
            'date_id' => 4, 
            'registration_status_id' => 1, 
        ]); 
        DB::table('plans')->insert([ 
            'code' => 'OM02-13-2022', 
            'name' => 'Este es un plan de mejora del estandar 02 y sería el 02 del año 2022 y semestre A, pero esta borrado', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2022-A', 
            'advance' => 58, 
            'duration' => 3, 
            'plan_status_id' => 2, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 2, 
            'user_id' => 3, 
            'date_id' => 4, 
            'registration_status_id' => 1, 
        ]);

        DB::table('plans')->insert([ 
            'code' => 'OM02-15-2023', 
            'name' => 'Este es un plan de mejora del estandar 02 y sería el 03 pero está borrado', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2023-A', 
            'advance' => 50, 
            'duration' => 2, 
            'plan_status_id' => 3, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 2, 
            'user_id' => 3, 
            'date_id' => 4, 
            'registration_status_id' => 3, 
        ]); 

        //2021 - B
        DB::table('plans')->insert([ 
            'code' => 'OM02-17-2023', 
            'name' => 'Este es un plan de mejora del estandar 02 y es el 01 del año 2022 - B', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2023-A', 
            'advance' => 50, 'duration' => 3, 
            'plan_status_id' => 3, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 2, 
            'user_id' => 3, 
            'date_id' => 2, 
            'registration_status_id' => 1, 
        ]); 
        DB::table('plans')->insert([ 
            'code' => 'OM02-18-2022', 
            'name' => 'Este es un plan de mejora del estandar 02 y sería el 02 del año 2022 y semestre A, pero esta borrado', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2022-A', 
            'advance' => 58, 
            'duration' => 3, 
            'plan_status_id' => 2, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 2, 
            'user_id' => 3, 
            'date_id' => 2, 
            'registration_status_id' => 1, 
        ]);

        DB::table('plans')->insert([ 
            'code' => 'OM02-19-2022', 
            'name' => 'Este es un plan de mejora del estandar 02 y sería el 03 pero está borrado', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2023-A', 
            'advance' => 50, 
            'duration' => 2, 
            'plan_status_id' => 3, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 2, 
            'user_id' => 3, 
            'date_id' => 2, 
            'registration_status_id' => 3, 
        ]); 

        //2021 - A
        DB::table('plans')->insert([ 
            'code' => 'OM02-20-2023', 
            'name' => 'Este es un plan de mejora del estandar 02 y es el 01 del año 2022 - B', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2023-A', 
            'advance' => 50, 'duration' => 3, 
            'plan_status_id' => 3, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 2, 
            'user_id' => 2, 
            'date_id' => 1, 
            'registration_status_id' => 1, 
        ]); 
        DB::table('plans')->insert([ 
            'code' => 'OM03-21-2022', 
            'name' => 'Este es un plan de mejora del estandar 02 y sería el 02 del año 2022 y semestre A, pero esta borrado', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2022-A', 
            'advance' => 58, 
            'duration' => 3, 
            'plan_status_id' => 2, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 2, 
            'user_id' => 2, 
            'date_id' => 1, 
            'registration_status_id' => 1, 
        ]);

        DB::table('plans')->insert([ 
            'code' => 'OM02-22-2023', 
            'name' => 'Este es un plan de mejora del estandar 02 y sería el 03 pero está borrado', 
            'opportunity_for_improvement' => 'Construir un nuevo pabellon xd', 
            'semester_execution' => '2023-A', 
            'advance' => 50, 
            'duration' => 2, 
            'plan_status_id' => 3, 
            'efficacy_evaluation' => 1, 
            'standard_id' => 2, 
            'user_id' => 2, 
            'date_id' => 1, 
            'registration_status_id' => 3, 
        ]); 
    }
}
