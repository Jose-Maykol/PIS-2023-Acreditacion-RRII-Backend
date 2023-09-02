<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot as RelationsPivot;

class RoleHasPermissionModel extends RelationsPivot
{

	protected $table ='roles_has_permissions';

    protected $fillable = [
        'permission_id',
        'role_id'
    ];
	public $timestamps = false;

    public function role()
    {
        return $this->belongsTo(RoleModel::class, 'role_id');
    }

    public function permission()
    {
        return $this->belongsTo(PermissionModel::class, 'permission_id');
    }
}
