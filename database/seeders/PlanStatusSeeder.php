<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PlanStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('plan_status')->insert([
            'description' => 'planificado',
            'registration_status_id' => 1,
        ]);
        DB::table('plan_status')->insert([
            'description' => 'en desarrollo',
            'registration_status_id' => 1,
        ]);
        DB::table('plan_status')->insert([
            'description' => 'completado',
            'registration_status_id' => 1,
        ]);
        
        DB::table('plan_status')->insert([
            'description' => 'postergado',
            'registration_status_id' => 1,

        ]);
        DB::table('plan_status')->insert([
            'description' => 'anulado',
            'registration_status_id' => 1,

        ]);
    }
}
