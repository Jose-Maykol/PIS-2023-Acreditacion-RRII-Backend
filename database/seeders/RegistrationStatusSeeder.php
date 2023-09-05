<?php

namespace Database\Seeders;

use App\Models\RegistrationStatusModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegistrationStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RegistrationStatusModel::create([
            'description' => 'activo'
        ]);
        RegistrationStatusModel::create([
            'description' => 'inactivo'
        ]);
        RegistrationStatusModel::create([
            'description' => 'borrado'
        ]);
    }
}
