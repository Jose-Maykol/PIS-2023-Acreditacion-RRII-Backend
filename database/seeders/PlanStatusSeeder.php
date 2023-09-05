<?php

namespace Database\Seeders;

use App\Models\PlanStatusModel;
use App\Models\RegistrationStatusModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PlanStatusModel::create([
            'description' => 'Planificado',
            'registration_status_id' => RegistrationStatusModel::select('id')->where('description', 'activo')->get()
        ]);
        PlanStatusModel::create([
            'description' => 'En Desarrollo',
            'registration_status_id' => RegistrationStatusModel::select('id')->where('description', 'activo')->get()
        ]);
        PlanStatusModel::create([
            'description' => 'Completado',
            'registration_status_id' => RegistrationStatusModel::select('id')->where('description', 'activo')->get()
        ]);
        PlanStatusModel::create([
            'description' => 'Postergado',
            'registration_status_id' => RegistrationStatusModel::select('id')->where('description', 'activo')->get()
        ]);
        PlanStatusModel::create([
            'description' => 'Anulado',
            'registration_status_id' => RegistrationStatusModel::select('id')->where('description', 'activo')->get()
        ]);
    }
}
