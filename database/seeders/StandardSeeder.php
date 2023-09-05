<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StandardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('standards')->insert([
            'name' => "Propósitos Articulados",
            'factor' => "FACTOR 1: PLANIFICACIÓN DEL PROGRAMA DE ESTUDIOS",
            'dimension' => "DIMENSIÓN 1: GESTIÓN ESTRATÉGICA",
            'related_standards' => "Estándar 2 (Participación de los grupos de interés)
            Estándar 5 (Pertinencia del perfil de egreso)",
            'nro_standard' => 1,
            'date_id' => 5,
            'registration_status_id' => 1
        ]);

        DB::table('standards')->insert([
            'name' => "Participación de los Grupos de Interés",
            'factor' => "FACTOR 1: PLANIFICACIÓN DEL PROGRAMA DE ESTUDIOS",
            'dimension' => "DIMENSIÓN 1: GESTIÓN ESTRATÉGICA",
            'related_standards' => "Estándar 1 (Propósitos del programa)
            Estándar 3 (Revisión periódica y participativa de las políticas y objetivos)",
            'nro_standard' => 2,
            'date_id' => 5,
            'registration_status_id' => 1
        ]);

        DB::table('standards')->insert([
            'name' => "Revisión periódica y participativa de las políticas y objetivos",
            'factor' => "FACTOR 1: PLANIFICACIÓN DEL PROGRAMA DE ESTUDIOS",
            'dimension' => "DIMENSIÓN 1: GESTIÓN ESTRATÉGICA",
            'related_standards' => "Estándar 2 (Participación de los grupos de interés)",
            'nro_standard' => 3,
            'date_id' => 5,
            'registration_status_id' => 1
        ]);

        DB::table('standards')->insert([
            'name' => "Sostenibilidad",
            'factor' => "FACTOR 1: PLANIFICACIÓN DEL PROGRAMA DE ESTUDIOS",
            'dimension' => "DIMENSIÓN 1: GESTIÓN ESTRATÉGICA",
            'related_standards' => "Estándar 3 (Revisión periódica y participativa de las políticas y objetivos)
            Estándar 28 (Equipamiento y uso de la infraestructura)",
            'nro_standard' => 4,
            'date_id' => 5,
            'registration_status_id' => 1
        ]);

        DB::table('standards')->insert([
            'name' => "Pertinencia del Perfil de Egreso",
            'factor' => "FACTOR 2: GESTIÓN DEL PERFIL DE EGRESO",
            'dimension' => "DIMENSIÓN 1: GESTIÓN ESTRATÉGICA",
            'related_standards' => "Estándar 1 (Propósitos articulados)
            Estándar 2 (Participación de los grupos de interés)",
            'nro_standard' => 5,
            'date_id' => 5,
            'registration_status_id' => 1
        ]);

        DB::table('standards')->insert([
            'name' => "Revisión del Perfil de Egreso",
            'factor' => "FACTOR 2: GESTIÓN DEL PERFIL DE EGRESO",
            'dimension' => "DIMENSIÓN 1: GESTIÓN ESTRATÉGICA",
            'related_standards' => "Estándar 2 (Participación de los grupos de interés)
            Estándar 5 (Pertinencia del perfil de egreso)",
            'nro_standard' => 6,
            'date_id' => 5,
            'registration_status_id' => 1
        ]);

        DB::table('standards')->insert([
            'name' => "Sistema de Gestión de la Calidad (SGC)",
            'factor' => "FACTOR 3: ASEGURAMIENTO DE LA CALIDAD",
            'dimension' => "DIMENSIÓN 1: GESTIÓN ESTRATÉGICA",
            'related_standards' => "Estándar 8 (Planes de mejora)
            Estándar 30 (Sistema de información y comunicación)",
            'nro_standard' => 7,
            'date_id' => 5,
            'registration_status_id' => 1
        ]);

        DB::table('standards')->insert([
            'name' => "Estándar para la gestión de calidad",
            'factor' => "FACTOR 3: ASEGURAMIENTO DE LA CALIDAD",
            'dimension' => "DIMENSIÓN 1: GESTIÓN ESTRATÉGICA",
            'related_standards' => "Estándar 7 (Sistema de gestión de la calidad)",
            'nro_standard' => 8,
            'date_id' => 5,
            'registration_status_id' => 1
        ]);
    }
}
