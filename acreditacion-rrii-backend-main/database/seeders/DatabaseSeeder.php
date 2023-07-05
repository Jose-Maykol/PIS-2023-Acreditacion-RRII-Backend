<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
		//roles
		\App\Models\Role::factory()->create([
            "name"=>"Admin"
         ]);
		 \App\Models\Role::factory()->create([
             "name"=>"User"
          ]);

        //Usuarios
		\App\Models\User::factory()->create([
            "name"=>"Omar",
            "lastname"=>"Apoyo",
            "email"=>"admin@unsa.edu.pe",
            "password"=>Hash::make("123456"),
			"estado"=>true,
         ]);
		 \App\Models\User::find(1)->roles()->attach(1);

		 \App\Models\User::factory()->create([
             "name"=>"Alexis",
             "lastname"=>"Arroyo",
             "email"=>"aarroyoh@unsa.edu.pe",
             "password"=>Hash::make("luisangel"),
			 "estado"=>true,
          ]);
		  \App\Models\User::find(2)->roles()->attach(1);

         \App\Models\User::factory()->create([
            "name"=>"Fernando",
            "lastname"=>"Araoz",
            "email"=>"faraoz@unsa.edu.pe",
            "password"=>Hash::make("123456"),
			"estado"=>true,
         ]);
		 \App\Models\User::find(3)->roles()->attach(1);

         \App\Models\User::factory()->create([
            "name"=>"Jhonatan",
            "lastname"=>"Acuña",
            "email"=>"jacuna@unsa.edu.pe",
            "password"=>Hash::make("123456"),
			"estado"=>true,
         ]);
		 \App\Models\User::find(4)->roles()->attach(1);

         \App\Models\User::factory()->create([
            "name"=>"Carlos",
            "lastname"=>"Gonzales",
            "email"=>"cgonzalesmo@unsa.edu.pe",
            "password"=>Hash::make("123456"),
			"estado"=>true,
         ]);
		 \App\Models\User::find(5)->roles()->attach(1);


         \App\Models\User::factory()->create([
            "name"=>"Christian",
            "lastname"=>"Sullca",
            "email"=>"csullcap@unsa.edu.pe",
            "password"=>Hash::make("123456"),
			"estado"=>true,
         ]);
		 \App\Models\User::find(6)->roles()->attach(1);

         \App\Models\User::factory()->create([
            "name"=>"Brayan",
            "lastname"=>"Guillen",
            "email"=>"bguillenn@unsa.edu.pe",
            "password"=>Hash::make("123456"),
			"estado"=>true,
         ]);
		 \App\Models\User::find(7)->roles()->attach(1);

         //Estandares

        \App\Models\Estandar::factory()->create([
            "name"=>"E-1 Propositos Articulados",
			"cabecera"=>"Cabecera de Estandar 1",
            "id_user"=>1,
         ]);
         \App\Models\Estandar::factory()->create([
            "name"=>"E-2 Participacion de los Grupos de Interes",
			"cabecera"=>"Cabecera de Estandar 2",
            "id_user"=>1,
         ]);
         \App\Models\Estandar::factory()->create([
            "name"=>"E-3 Revision Periodica  de las Politicas y Objetivos",
			"cabecera"=>"Cabecera de Estandar 3",
            "id_user"=>1,
         ]);
         \App\Models\Estandar::factory()->create([
            "name"=>"E-4 Sostenibilidad",
			"cabecera"=>"Cabecera de Estandar 4",
            "id_user"=>1,
         ]);
         \App\Models\Estandar::factory()->create([
            "name"=>"E-5 Pertinencia del Perfil de Egreso",
			"cabecera"=>"Cabecera de Estandar 5",
            "id_user"=>1,
         ]);
         \App\Models\Estandar::factory()->create([
            "name"=>"E-6 Revision del Perfil de Egreso",
			"cabecera"=>"Cabecera de Estandar 6",
            "id_user"=>1,
         ]);
         \App\Models\Estandar::factory()->create([
            "name"=>"E-7 Sistema de Gestion de la Calidad",
			"cabecera"=>"Cabecera de Estandar 7",
            "id_user"=>1,
         ]);
         \App\Models\Estandar::factory()->create([
            "name"=>"E-8 Planes de Mejora",
			"cabecera"=>"Cabecera de Estandar 8",
            "id_user"=>1,
         ]);
         \App\Models\Estandar::factory()->create([
            "name"=>"E-9 Planes de Mejora",
			"cabecera"=>"Cabecera de Estandar 9",
            "id_user"=>1,
         ]);

         //Fuentes valores

         \App\Models\FuentesValores::factory()->create([
            "valor"=>"Solicitudes de acción correctiva",
         ]);
         \App\Models\FuentesValores::factory()->create([
            "valor"=>"Servicios no conformes",
         ]);
         \App\Models\FuentesValores::factory()->create([
            "valor"=>"Quejas",
         ]);
         \App\Models\FuentesValores::factory()->create([
            "valor"=>"Evaluación de competencias",
         ]);
         \App\Models\FuentesValores::factory()->create([
            "valor"=>"Evaluación de los objetivos Educacionales",
         ]);
         \App\Models\FuentesValores::factory()->create([
            "valor"=>"Actividades diarias",
         ]);
         \App\Models\FuentesValores::factory()->create([
            "valor"=>"Lineamientos institucionales",
         ]);
         \App\Models\FuentesValores::factory()->create([
            "valor"=>"Acuerdos de Consejo de Facultad y Asamblea Docente",
         ]);
         \App\Models\FuentesValores::factory()->create([
            "valor"=>"Buenas prácticas de otras organizaciones",
         ])
         ;\App\Models\FuentesValores::factory()->create([
            "valor"=>"Otros",
         ]);

         //Responsalbles valores

         ;\App\Models\ResponsablesValores::factory()->create([
            "valor"=>"Dirección EP RR.II.",
         ]);
         ;\App\Models\ResponsablesValores::factory()->create([
            "valor"=>"Comisión de desarrollo docente",
         ]);

         // Estados valores : Planificado, Programado, Reprogramado, En proceso o Concluido.

         ;\App\Models\EstadosValores::factory()->create([
            "valor"=>"Planificado",
         ]);
         ;\App\Models\EstadosValores::factory()->create([
            "valor"=>"Programado",
         ]);
         ;\App\Models\EstadosValores::factory()->create([
            "valor"=>"Reprogramado",
         ]);
         ;\App\Models\EstadosValores::factory()->create([
            "valor"=>"En proceso",
         ]);
         ;\App\Models\EstadosValores::factory()->create([
            "valor"=>"Concluido",
         ]);

    }
}
