<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class RoleModel extends Model
{
    use HasFactory;

	protected $table ='roles';

    protected $fillable = [
        'name'
    ];
	public $timestamps = false;

	public function users(){
        return $this->belongsToMany(UserModel::class,'user_id');
    }
    public function permissions() {
        return $this->belongsToMany(PermissionModel::class, 'roles_has_permissions', 'role_id', 'permission_id')->using(RoleHasPermissionModel::class);
    }
    public static function roleAdmin(){
        return self::where('name', 'Administrador')->value('id');
    }
    
}
