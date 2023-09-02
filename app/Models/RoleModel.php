<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleModel extends Model
{
    use HasFactory;

	protected $table ='roles';

    protected $fillable = [
        'name'
    ];
	public $timestamps = false;

	public function users(){
        return $this->belongsToMany(UserModel::class,'role_id');
    }
}
