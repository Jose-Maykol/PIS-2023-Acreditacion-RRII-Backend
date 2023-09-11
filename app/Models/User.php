<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

<<<<<<< HEAD:app/Models/UserModel.php
use App\Models\RoleModel;

class UserModel extends Authenticatable
=======
class User extends Authenticatable
>>>>>>> development:app/Models/User.php
{
  use HasApiTokens, HasFactory, Notifiable;


  protected $table = 'users';

  protected $fillable = [
    'name',
    'lastname',
    'email',
    'password',
    'role_id',
	  'registration_status_id',
  ];

  public $timestamps = false;

  public function standards() {
    return $this->belongsToMany(StandardModel::class, 'users_standards', 'user_id', 'standard_id')->using(UserStandardModel::class);
  }
  public function standard($user_id) {
    return User::find($user_id)->standards()->first();
  }

  public function role()
  {
    return $this->belongsTo(RoleModel::class, 'role_id');
  }

  public function hasPermission2($permission){

    return $this->role()->first()->permissions()->where('name', $permission)->exists();
  }
  public function hasPermission($permission){

    return $this->role()->first()->permissions()->where('name', $permission)->exists();
  }

  public function plans()
  {
    return $this->hasMany(PlanModel::class, 'user_id');
  }
  public function evidences()
  {
    return $this->hasMany(Evidence::class, 'id');
  }
  public function providers()
  {
    return $this->hasMany(Provider::class, 'id_user');
  }

<<<<<<< HEAD:app/Models/UserModel.php
  

  public function isAdmin()
  {
    return $this->role()->first()->where('name', 'Admin')->exists();
=======
  public function isRole($role) {
    return $this->role()->where('name', $role)->exists();
  }
  public function isAdmin() {
    return $this->role()->where('name', 'administrador')->exists();
>>>>>>> development:app/Models/User.php
  }

  public function isCreatorPlan($plan_id) {
    return $this->plans()->where('id', $plan_id)->exists();
  }

  public function isAssignStandard($standard_id)
  {
    return $this->standards()->first()->where('id', $standard_id)->exists();
  }
}
