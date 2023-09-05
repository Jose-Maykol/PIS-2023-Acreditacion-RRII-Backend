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
            'description' => 'Planificado',
            'registration_status_id' => 1,
        ]);
        DB::table('plan_status')->insert([
            'description' => 'Terminado',
            'registration_status_id' => 1,
        ]);
        DB::table('plan_status')->insert([
            'description' => 'En proceso',
            'registration_status_id' => 1,
        ]);
    }
}
