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
    public static function exists($standard_id){
        return self::where('id', $standard_id)->exists();
    }
    public static function isActive($standard_id){
        return self::where('id', $standard_id)->exists();
    }
    public static function existsAndActive($standard_id){
        return self::exists($standard_id) and self::isActive($standard_id);
    }
}
