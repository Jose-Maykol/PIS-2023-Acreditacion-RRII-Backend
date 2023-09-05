<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NarrativesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('narratives')->insert([
            'content' => 'Esta es una narrativa bonita que se creó hace mucho tiempo',
            'date_id' => 5,
            'standard_id' => 1,
            'registration_status_id' => 1,
        ]);
    }
}
