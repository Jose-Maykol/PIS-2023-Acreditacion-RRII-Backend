<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersStandardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users_standards')->insert([
            'user_id' => 1,
            'standard_id' => 1
        ]);

        DB::table('users_standards')->insert([
            'user_id' => 2,
            'standard_id' => 2
        ]);

        DB::table('users_standards')->insert([
            'user_id' => 3,
            'standard_id' => 3
        ]);

        DB::table('users_standards')->insert([
            'user_id' => 4,
            'standard_id' => 4
        ]);

        DB::table('users_standards')->insert([
            'user_id' => 5,
            'standard_id' => 5
        ]);
        
        DB::table('users_standards')->insert([
            'user_id' => 6,
            'standard_id' => 6
        ]);

        DB::table('users_standards')->insert([
            'user_id' => 7,
            'standard_id' => 7
        ]);

        DB::table('users_standards')->insert([
            'user_id' => 8,
            'standard_id' => 8
        ]);
    }
}
