<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvidenceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('evidence_types')->insert([
            'description' => 'planificación',
        ]);
        DB::table('evidence_types')->insert([
            'description' => 'resultado',
        ]);
        DB::table('evidence_types')->insert([
            'description' => 'mejora',
        ]);
    }
}
