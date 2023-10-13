<?php

namespace App\Repositories;

use App\Models\User;

use Spatie\Permission\Models\Role;

class RoleRepository
{
    public function checkIfRoleExists($roleName)
    {
        return Role::where('name', $roleName)->exists();
    }
}