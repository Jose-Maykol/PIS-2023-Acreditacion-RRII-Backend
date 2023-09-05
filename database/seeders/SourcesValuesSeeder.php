<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SourcesValuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sources_values')->insert([
            'value' => 'Solicitudes de acción correctiva',
        ]);
        DB::table('sources_values')->insert([
            'value' => 'Servicios no conformes',
        ]);
        DB::table('sources_values')->insert([
            'value' => 'Quejas',
        ]);
        DB::table('sources_values')->insert([
            'value' => 'Evaluación de competencias',
        ]);
        DB::table('sources_values')->insert([
            'value' => 'Evaluación de los objetivos Educacionales',
        ]);
        DB::table('sources_values')->insert([
            'value' => 'Actividades diarias',
        ]);
        DB::table('sources_values')->insert([
            'value' => 'Lineamientos institucionales',
        ]);
        DB::table('sources_values')->insert([
            'value' => 'Acuerdos de Consejo de Facultad y Asamblea Docente',
        ]);
        DB::table('sources_values')->insert([
            'value' => 'Buenas prácticas de otras organizaciones',
        ]);
        DB::table('sources_values')->insert([
            'value' => 'Otros',
        ]);
    }
}
