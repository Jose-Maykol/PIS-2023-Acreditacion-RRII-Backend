<?php

namespace Database\Seeders;

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
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Arian',
            'lastname' => 'Canaza',
            'email'=> 'acanazacua@unsa.edu.pe',
            'password' => 'qwerty',
            'role_id' => 1,
            'registration_status_id' => 1
        ]);

        DB::table('users')->insert([
            'name' => 'José',
            'lastname' => 'Paniura',
            'email'=> 'jpaniura@unsa.edu.pe',
            'password' => 'qwerty',
            'role_id' => 1,
            'registration_status_id' => 1
        ]);

        DB::table('users')->insert([
            'name' => 'Angel',
            'lastname' => 'Zevallos',
            'email'=> 'azevallosa@unsa.edu.pe',
            'password' => 'qwerty',
            'role_id' => 1,
            'registration_status_id' => 1
        ]);

        DB::table('users')->insert([
            'name' => 'Sofia',
            'lastname' => 'Onque',
            'email'=> 'sonque@unsa.edu.pe',
            'password' => 'qwerty',
            'role_id' => 1,
            'registration_status_id' => 1
        ]);

        DB::table('users')->insert([
            'name' => 'Percy',
            'lastname' => 'Flores',
            'email'=> 'pfloresq@unsa.edu.pe',
            'password' => 'qwerty',
            'role_id' => 1,
            'registration_status_id' => 1
        ]);

        DB::table('users')->insert([
            'name' => 'Paulina',
            'lastname' => 'Choqueneira',
            'email'=> 'pchoqueneira@unsa.edu.pe',
            'password' => 'qwerty',
            'role_id' => 1,
            'registration_status_id' => 1
        ]);

        DB::table('users')->insert([
            'name' => 'Alex',
            'lastname' => 'Turpo',
            'email'=> 'aturpoco@unsa.edu.pe',
            'password' => 'qwerty',
            'role_id' => 1,
            'registration_status_id' => 1
        ]);

        DB::table('users')->insert([
            'name' => 'Walter',
            'lastname' => 'Huaracha',
            'email'=> 'whuaracha@unsa.edu.pe',
            'password' => 'qwerty',
            'role_id' => 1,
            'registration_status_id' => 1
        ]);
    }
}
