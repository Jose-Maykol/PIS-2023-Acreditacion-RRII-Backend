<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvidencesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('evidences')->insert([
            'name' => 'nombre de la evidencia',
            'path' => 'wenopath/',
            'type' => 'tipo',
            'size' => 'tamaño',
            'user_id' => 2,
            'folder_id' => 1,
            'evidence_type_id' => 2,
            'standard_id' => 2,
            'date_id' => 5
        ]);
    }
}
