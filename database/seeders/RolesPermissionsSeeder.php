<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role as RoleModel;
use Spatie\Permission\Models\Permission as PermissionModel;
class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role1 = RoleModel::create(['name' => 'administrador']);
        $role2 = RoleModel::create(['name' => 'docente']);
        
        $permissions = [
            //standard
            ['name' => 'create_standard'],
            ['name' => 'read_standard'],
            ['name' => 'update_standard'],
            ['name' => 'delete_standard'],
            //plan
            ['name' => 'create_plan'],
            ['name' => 'read_plan'],
            ['name' => 'update_plan'],
            ['name' => 'delete_plan'],
            //evidence
            ['name' => 'create_evidence'],
            ['name' => 'read_evidence'],
            ['name' => 'update_evidence'],
            ['name' => 'delete_evidence'],
            //user
            ['name' => 'create_user'],
            ['name' => 'read_user'],
            ['name' => 'update_user'],
            ['name' => 'delete_user']
        ];

        foreach ($permissions as $permission){
            PermissionModel::create($permission);
        };

        $permission_administrador = PermissionModel::all();
        $role1->givePermissionTo($permission_administrador);

        $permission_docente = PermissionModel::whereIn('name', [
            'read_standard',
            'create_plan', 'read_plan', 'update_plan', 'delete_plan',
            'create_evidence', 'read_evidence', 'update_evidence', 'delete_evidence',
            'read_user'
        ])->get();
        $role2->syncPermissions($permission_docente);
        
        
    }
}
