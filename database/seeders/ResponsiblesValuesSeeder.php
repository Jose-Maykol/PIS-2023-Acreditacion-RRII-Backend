<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResponsiblesValuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('responsibles_values')->insert([
            'value' => 'Dirección EP RR.II.',
        ]);

        DB::table('responsibles_values')->insert([
            'value' => 'Comisión de desarrollo docente',
        ]);
    }
}
