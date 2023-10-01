<?php

namespace Database\Seeders;

use App\Models\RegistrationStatusModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class StandardStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('standard_status')->insert([
            'description' => 'no logrado',
            'registration_status_id' => RegistrationStatusModel::registrationActiveId(),
        ]);
        DB::table('standard_status')->insert([
            'description' => 'logrado',
            'registration_status_id' => RegistrationStatusModel::registrationActiveId(),
        ]);
        DB::table('standard_status')->insert([
            'description' => 'logrado satisfactoriamente',
            'registration_status_id' => RegistrationStatusModel::registrationActiveId(),
        ]);
    }
}
