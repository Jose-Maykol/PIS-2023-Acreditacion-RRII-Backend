<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProblemsOpportunitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('problems_opportunities')->insert([
            'description' => 'Este es un problema oportunidad',
            'plan_id' => 1,
            'registration_status_id' => 1,
        ]);
        DB::table('problems_opportunities')->insert([
            'description' => 'Este es otro problema oportunidad',
            'plan_id' => 1,
            'registration_status_id' => 1,
        ]);
    }
}
