<?php

namespace Database\Seeders;

use App\Models\RegistrationStatusModel;
use App\Models\RoleModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        DB::table('users')->insert([
            'name' => 'ARIAN EDUARDO JAVIER',
            'lastname' => 'CANAZA CUADROS',
            'email'=> 'acanazacua@unsa.edu.pe',
            'password' => 'null',
            'role_id' => RoleModel::where('name', 'administrador')->value('id'),
            'registration_status_id' => RegistrationStatusModel::where('description', 'activo')->value('id')
        ]);

        DB::table('users')->insert([
            'name' => 'JOSE MAYKOL',
            'lastname' => 'PANIURA HUAMANI',
            'email'=> 'jpaniura@unsa.edu.pe',
            'password' => 'null',
            'role_id' => RoleModel::where('name', 'administrador')->value('id'),
            'registration_status_id' => RegistrationStatusModel::where('description', 'activo')->value('id')
        ]);

        DB::table('users')->insert([
            'name' => 'ANGEL ALEXIS',
            'lastname' => 'ZEVALLOS APAZA',
            'email'=> 'azevallosa@unsa.edu.pe',
            'password' => 'null',
            'role_id' => RoleModel::where('name', 'administrador')->value('id'),
            'registration_status_id' => RegistrationStatusModel::where('description', 'activo')->value('id')
        ]);

        DB::table('users')->insert([
            'name' => 'SOFIA SAIR',
            'lastname' => 'ONQUE GARATE',
            'email'=> 'sonque@unsa.edu.pe',
            'password' => 'null',
            'role_id' => RoleModel::where('name', 'administrador')->value('id'),
            'registration_status_id' => RegistrationStatusModel::where('description', 'activo')->value('id')
        ]);

        DB::table('users')->insert([
            'name' => 'PERCY SANTIAGO',
            'lastname' => 'FLORES QUISPE',
            'email'=> 'pfloresq@unsa.edu.pe',
            'password' => 'null',
            'role_id' => RoleModel::where('name', 'administrador')->value('id'),
            'registration_status_id' => RegistrationStatusModel::where('description', 'activo')->value('id')
        ]);

        DB::table('users')->insert([
            'name' => 'PAULINA MIRIAM',
            'lastname' => 'CHOQUENEIRA CCASA',
            'email'=> 'pchoqueneira@unsa.edu.pe',
            'password' => 'null',
            'role_id' => RoleModel::where('name', 'administrador')->value('id'),
            'registration_status_id' => RegistrationStatusModel::where('description', 'activo')->value('id')
        ]);

        DB::table('users')->insert([
            'name' => 'ALEX RONALDO',
            'lastname' => 'TURPO COILA',
            'email'=> 'aturpoco@unsa.edu.pe',
            'password' => 'null',
            'role_id' => RoleModel::where('name', 'administrador')->value('id'),
            'registration_status_id' => RegistrationStatusModel::where('description', 'activo')->value('id')
        ]);

        DB::table('users')->insert([
            'name' => 'WALTER',
            'lastname' => 'HUARACHA CONDORI',
            'email'=> 'whuaracha@unsa.edu.pe',
            'password' => 'null',
            'role_id' => RoleModel::where('name', 'administrador')->value('id'),
            'registration_status_id' => RegistrationStatusModel::where('description', 'activo')->value('id')
        ]);
    }
}
