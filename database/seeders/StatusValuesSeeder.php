<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusValuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status_values')->insert([
            'value' => 'Planificado',
        ]);
        DB::table('status_values')->insert([
            'value' => 'Programado',
        ]);
        DB::table('status_values')->insert([
            'value' => 'Reprogramado',
        ]);
        DB::table('status_values')->insert([
            'value' => 'En proceso',
        ]);
        DB::table('status_values')->insert([
            'value' => 'Concluido',
        ]);
    }
}
