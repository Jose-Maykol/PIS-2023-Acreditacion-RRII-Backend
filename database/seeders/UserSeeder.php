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
        $users = [
            [
                'name' => 'ARIAN EDUARDO JAVIER',
                'lastname' => 'CANAZA CUADROS',
                'email'=> 'acanazacua@unsa.edu.pe',
                'password' => 'null',
                'registration_status_id' => RegistrationStatusModel::registrationActiveId()
            ],
            [
                'name' => 'JOSE MAYKOL',
                'lastname' => 'PANIURA HUAMANI',
                'email'=> 'jpaniura@unsa.edu.pe',
                'password' => 'null',
                'registration_status_id' => RegistrationStatusModel::registrationActiveId()
            ],
            [
                'name' => 'ANGEL ALEXIS',
                'lastname' => 'ZEVALLOS APAZA',
                'email'=> 'azevallosa@unsa.edu.pe',
                'password' => 'null',
                'registration_status_id' => RegistrationStatusModel::registrationActiveId()
            ].
            [
                'name' => 'SOFIA SAIR',
                'lastname' => 'ONQUE GARATE',
                'email'=> 'sonque@unsa.edu.pe',
                'password' => 'null',
                'registration_status_id' => RegistrationStatusModel::registrationActiveId()
            ].
            [
                'name' => 'PERCY SANTIAGO',
                'lastname' => 'FLORES QUISPE',
                'email'=> 'pfloresq@unsa.edu.pe',
                'password' => 'null',
                'registration_status_id' => RegistrationStatusModel::registrationActiveId()
            ],
            [
                'name' => 'PAULINA MIRIAM',
                'lastname' => 'CHOQUENEIRA CCASA',
                'email'=> 'pchoqueneira@unsa.edu.pe',
                'password' => 'null',
                'registration_status_id' => RegistrationStatusModel::registrationActiveId()
            ],
            [
                'name' => 'ALEX RONALDO',
                'lastname' => 'TURPO COILA',
                'email'=> 'aturpoco@unsa.edu.pe',
                'password' => 'null',
                'registration_status_id' => RegistrationStatusModel::registrationActiveId()
            ],
            [
                'name' => 'WALTER',
                'lastname' => 'HUARACHA CONDORI',
                'email'=> 'whuaracha@unsa.edu.pe',
                'password' => 'null',
                'registration_status_id' => RegistrationStatusModel::registrationActiveId()
            ]
        ];

        foreach ($users as $user) {
            $user = User::create($user);
            $user->assignRole(RoleModel::findByName('administrador'));
        }
    }
}
