<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationStatusModel extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table ='registration_status';
    protected $fillable = [
        'description'
    ];
    public static function registrationDelete(){
        return self::where('description', 'borrado')->value('id');
    }
    public static function registrationActive(){
        return self::where('description', 'activo')->value('id');
    }
    public static function registrationInactive(){
        return self::where('description', 'inactivo')->value('id');
    }
    public static function registrationBeforeDelete(){
        return self::where('description', 'antes_de_borrar')->value('id');
    }
    public function isActive(){
        return $this->where('description','activo');
    }
}//2023/A