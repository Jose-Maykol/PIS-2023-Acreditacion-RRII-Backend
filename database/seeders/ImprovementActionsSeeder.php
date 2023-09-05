<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImprovementActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('improvement_actions')->insert([
            'description' => 'esta es una acción de mejora del plan de mejora 1',
            'plan_id' => 1,
            'registration_status_id' => 1,
        ]);
    }
}
