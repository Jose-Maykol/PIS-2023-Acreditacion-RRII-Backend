<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
		
      $this->call([
         RegistrationStatusSeeder::class,
         DateSeeder::class,
         EvidenceTypeSeeder::class,
         PlanStatusSeeder::class,
         StandardStatusSeeder::class,
         RolesPermissionsSeeder::class,
         StandardSeeder::class,        
         UserSeeder::class,
      ]);
    }
}
