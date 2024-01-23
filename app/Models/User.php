<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable, HasRoles;


  protected $table = 'users';

  protected $fillable = [
    'name',
    'lastname',
    'email',
    'password',
	  'registration_status_id',
  ];
  protected $hidden = [
    'password',
    'roles'
  ];

  public $timestamps = false;

  public function standards() {
    return $this->belongsToMany(StandardModel::class, 'users_standards', 'user_id', 'standard_id')->using(UserStandardModel::class);
  }
  public function standard($user_id) {
    return User::find($user_id)->standards()->first();
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
  public function isAdmin() {
    return $this->hasRole('administrador');
  }

  
  public function isCreatorPlan($plan_id) {
    return $this->plans()->where('id', $plan_id)->exists();
  }

  public function isAssignStandard($standard_id)
  {
    return $this->standards()->where('standards.id', $standard_id)->exists();
  }
}
