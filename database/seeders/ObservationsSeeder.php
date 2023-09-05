<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObservationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('observations')->insert([
            'description' => 'Esta es una observacion bonita',
            'plan_id' => 1,
            'registration_status_id' => 1,
        ]);
    }
}
