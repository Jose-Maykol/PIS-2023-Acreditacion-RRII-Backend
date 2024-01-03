<?php

namespace Database\Seeders;

use App\Models\RegistrationStatusModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('date_semesters')->insert([
            'year' => 2023,
            'semester' => 'A',
            'is_closed' => false,
            'registration_status_id' => RegistrationStatusModel::registrationActiveId(),
        ]);
        DB::table('date_semesters')->insert([
            'year' => 2023,
            'semester' => 'B',
            'is_closed' => false,
            'registration_status_id' => RegistrationStatusModel::registrationActiveId(),
        ]);
        DB::table('date_semesters')->insert([
            'year' => 2024,
            'semester' => 'A',
            'is_closed' => false,
            'registration_status_id' => RegistrationStatusModel::registrationActiveId(),
        ]);
    }
}
