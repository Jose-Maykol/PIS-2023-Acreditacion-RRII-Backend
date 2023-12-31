<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class RegistrationStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('registration_status')->insert([
            'description' => "activo"
        ]);
        DB::table('registration_status')->insert([
            'description' => "inactivo"
        ]);
        DB::table('registration_status')->insert([
            'description' => "pendiente de autenticación"
        ]);
        
    }
}
