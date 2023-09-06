<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('permissions')->insert([
            'name' => 'Usted no puede eliminar'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Usted no puede modificar'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Usted no tiene permisos para visualizar este archivo'
        ]);
        DB::table('permissions')->insert([
            'name' => 'Usted no tiene permisos para crear'
        ]);

        //Admin - user_update, user_create, 
        //Docente -standard_read - standard_update,  plan_update, plan_create, plan_delete, plan_read
    }
}
