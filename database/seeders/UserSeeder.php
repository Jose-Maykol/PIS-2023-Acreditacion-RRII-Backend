<?php

namespace Database\Seeders;

use App\Models\RegistrationStatusModel;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role as RoleModel;
use Spatie\Permission\Models\Permission as PermissionModel;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $user = User::create([
            'name' => 'ARIAN EDUARDO JAVIER',
            'lastname' => 'CANAZA CUADROS',
            'email'=> 'acanazacua@unsa.edu.pe',
            'password' => 'null',
            'registration_status_id' => RegistrationStatusModel::registrationActive()
        ]);

        $user->assignRole(RoleModel::findByName('administrador'));

        DB::table('users')->insert([
            'name' => 'JOSE MAYKOL',
            'lastname' => 'PANIURA HUAMANI',
            'email'=> 'jpaniura@unsa.edu.pe',
            'password' => 'null',
            'registration_status_id' => RegistrationStatusModel::registrationActive()
        ]);

        DB::table('users')->insert([
            'name' => 'ANGEL ALEXIS',
            'lastname' => 'ZEVALLOS APAZA',
            'email'=> 'azevallosa@unsa.edu.pe',
            'password' => 'null',
            'registration_status_id' => RegistrationStatusModel::registrationActive()
        ]);

        DB::table('users')->insert([
            'name' => 'SOFIA SAIR',
            'lastname' => 'ONQUE GARATE',
            'email'=> 'sonque@unsa.edu.pe',
            'password' => 'null',
            'registration_status_id' => RegistrationStatusModel::registrationActive()
        ]);

        DB::table('users')->insert([
            'name' => 'PERCY SANTIAGO',
            'lastname' => 'FLORES QUISPE',
            'email'=> 'pfloresq@unsa.edu.pe',
            'password' => 'null',
            'registration_status_id' => RegistrationStatusModel::registrationActive()
        ]);

        DB::table('users')->insert([
            'name' => 'PAULINA MIRIAM',
            'lastname' => 'CHOQUENEIRA CCASA',
            'email'=> 'pchoqueneira@unsa.edu.pe',
            'password' => 'null',
            'registration_status_id' => RegistrationStatusModel::registrationActive()
        ]);

        DB::table('users')->insert([
            'name' => 'ALEX RONALDO',
            'lastname' => 'TURPO COILA',
            'email'=> 'aturpoco@unsa.edu.pe',
            'password' => 'null',
            'registration_status_id' => RegistrationStatusModel::registrationActive()
        ]);

        DB::table('users')->insert([
            'name' => 'WALTER',
            'lastname' => 'HUARACHA CONDORI',
            'email'=> 'whuaracha@unsa.edu.pe',
            'password' => 'null',
            'registration_status_id' => RegistrationStatusModel::registrationActive()
        ]);
    }
}
