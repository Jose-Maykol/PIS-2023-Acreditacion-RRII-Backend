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
         UserSeeder::class,
         DateSeeder::class,
         StandardSeeder::class,
         //ActasSeeder::class,
         EvidenceTypeSeeder::class,
         PlanStatusSeeder::class,
         //PlanSeeder::class,
         //FolderSeeder::class,
         //EvidencesSeeder::class,
         //GoalsSeeder::class,
         //ImprovementActionsSeeder::class,
         //ObservationsSeeder::class,
         //ProblemsOpportunitiesSeeder::class,
         //ResourcesSeeder::class,
         //ResponsiblesSeeder::class,
         //RootCausesSeeder::class,
         //SourcesSeeder::class,
         PermissionsSeeder::class,
         RolesPermissionsSeeder::class,        
         //ResponsiblesValuesSeeder::class,
         //SourcesValuesSeeder::class,
         //StatusValuesSeeder::class,
         UsersStandardsSeeder::class,
      ]);
    }
}
