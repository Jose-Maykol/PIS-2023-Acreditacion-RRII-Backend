<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleHasPermissionModel extends Model
{
    use HasFactory;

	protected $table ='role_has_permissions';

    protected $fillable = [
        'permissions_id',
        'role_id'
    ];
	public $timestamps = false;

}
