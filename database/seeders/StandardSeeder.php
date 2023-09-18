<?php

namespace Database\Seeders;

use App\Models\DateModel;
use App\Models\RegistrationStatusModel;
use App\Models\StandardModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StandardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //2023-A
        StandardModel::create34Standards(2023,'A');
        StandardModel::create34Standards(2023,'B');

    }
}
