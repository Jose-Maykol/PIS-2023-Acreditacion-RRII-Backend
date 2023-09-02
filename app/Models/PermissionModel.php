<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PermissionModel extends Model
{
    use HasFactory;

	protected $table ='permissions';

    protected $fillable = [
        'name'
    ];
	public $timestamps = false;

    public function roles()
    {
        return $this->belongsToMany(RoleModel::class, 'roles_has_permissions', 'role_id', 'permission_id')->using(RoleHasPermissionModel::class);
    }
}
