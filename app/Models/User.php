<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
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

  public function estandars()
  {
    return $this->hasMany(Estandar::class, 'id');
  }
  public function plans()
  {
    return $this->hasMany(Plan::class, 'id');
  }
  public function evidencias()
  {
    return $this->hasMany(Evidencia::class, 'id');
  }
  public function providers()
  {
    return $this->hasMany(Provider::class, 'id_user');
  }

  public function roles()
  {
    return $this->belongsToMany(Role::class, 'role_user', 'id_user', 'id_rol');
  }

  public function isAdmin()
  {
    return $this->roles()->where('name', 'Admin')->exists();
  }

  public function isCreatorPlan($plan_id)
  {
    return PlanModel::where('id', $plan_id)->where('user_id', $this->id)->exists();
  }

  public function isEncargadoEstandar($id_estandar)
  {
    return Estandar::where('id', $id_estandar)->where('id_user', $this->id)->exists();
  }
}
