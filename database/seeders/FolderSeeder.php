<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('folders')->insert([
            'name' => 'Prueba',
            'path' => 'Prueba/',
            'user_id' => 2,
            //parent_id' => null,
            'evidence_type_id' => 1,  
            'standard_id' => 2,
            'date_id' => 5,
        ]);

        DB::table('folders')->insert([
            'name' => 'Carpeta',
            'path' => 'Carpeta/',
            'user_id' => 3,
            //parent_id' => null,
            'evidence_type_id' => 1,  
            'standard_id' => 1,
            'date_id' => 5,
        ]);
    }
}
