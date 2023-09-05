<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function PHPSTORM_META\map;

class StandardModel extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table ='standards';
    protected $fillable = [
        'name',
        'factor',
        'dimension',
        'related_standards',
        'nro_standard',
		'date_id',
        'registration_status_id'
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
