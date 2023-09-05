<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RootCausesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('root_causes')->insert([
            'description' => 'Esta es una causa raíz',
            'plan_id' => 1,
            'registration_status_id' => 1
        ]);
    }
}
