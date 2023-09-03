<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\RoleModel;

class UserModel extends Authenticatable
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

  public function role()
  {
    return $this->belongsTo(RoleModel::class, 'role_id');
  }

  public function hasPermission($permission){

    return $this->role()->first()->permissions()->where('name', $permission)->exists();
  }

  public function standards()
  {
    return $this->hasMany(StandardModel::class, 'id');
  }
  public function plans()
  {
    return $this->hasMany(PlanModel::class, 'id');
  }
  public function evidences()
  {
    return $this->hasMany(Evidence::class, 'id');
  }
  public function providers()
  {
    return $this->hasMany(Provider::class, 'id_user');
  }

  

  public function isAdmin()
  {
    return $this->role()->first()->where('name', 'Admin')->exists();
  }

  public function isCreatorPlan($plan_id)
  {
    return PlanModel::where('id', $plan_id)->where('user_id', $this->id)->exists();
  }

  public function isAssignStandard($standard_id)
  {
    return StandardModel::where('id', $standard_id)->where('user_id', $this->id)->exists();
  }
}
