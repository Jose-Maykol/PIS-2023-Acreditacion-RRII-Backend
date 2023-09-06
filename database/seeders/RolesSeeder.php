<?php

namespace Database\Seeders;

use App\Models\RoleModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RoleModel::create([
            'name' => 'Docente'
        ]);
        RoleModel::create([
            'name' => 'Administrador'
        ]);
    }
}
