<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('actas')->insert([
            'description' => "Esta es una acta con descripción de 5 palabras",
            'date' => '01-09-2023',
            'standard_id' => 5 
        ]);
    }
}
