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
		//roles
		\App\Models\RoleModel::factory()->create([
            "name"=>"Admin"
      ]);
		\App\Models\RoleModel::factory()->create([
            "name"=>"User"
      ]);

		//registration status
      $this->call([
         RegistrationStatusSeeder::class,
         UserSeeder::class,
         DateSeeder::class,
         StandardSeeder::class,
         ActasSeeder::class,
         EvidenceTypeSeeder::class,
         PlanStatusSeeder::class,
         PlanSeeder::class,
         FolderSeeder::class,
         EvidencesSeeder::class,
         GoalsSeeder::class,
         ImprovementActionsSeeder::class,
         NarrativesSeeder::class,
         ObservationsSeeder::class,
         PermissionsSeeder::class,
         ProblemsOpportunitiesSeeder::class,
         ResourcesSeeder::class,
         ResponsiblesSeeder::class,
         ResponsiblesValuesSeeder::class,
         RolesPermissionsSeeder::class,
         RootCausesSeeder::class,
         SourcesSeeder::class,
         SourcesValuesSeeder::class,
         StatusValuesSeeder::class,
         UsersStandardsSeeder::class,
      ]);
    }
}
