<?php

namespace Database\Seeders;

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
            'semester' => 'A'
        ]);
        DB::table('date_semesters')->insert([
            'year' => 2023,
            'semester' => 'B'
        ]);
    }
}
