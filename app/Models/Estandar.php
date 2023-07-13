<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estandar extends Model
{

    use HasFactory;
    public $timestamps = true;
    protected $table ='estandars';
    protected $fillable = [
        'name',
        'position',
		'cabecera'
    ];


    public function users(){
        return $this->belongsTo(User::class,'id_user');
    }
    public function plans(){
        return $this->hasMany(plan::class,'id_estandar');
    }
	public function narrativas(){
        return $this->hasMany(narrativa::class,'id_narrativa');
    }

}
