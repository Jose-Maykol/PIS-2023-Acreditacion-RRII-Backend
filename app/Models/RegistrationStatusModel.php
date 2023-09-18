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
    public static function registrationActive(){
        return self::where('description', 'activo')->value('id');
    }
    public static function registrationInactive(){
        return self::where('description', 'inactivo')->value('id');
    }
    public static function registrationAuthenticationPending(){
        return self::where('description', 'pendiente de autenticación')->value('id');
    }
    public static function registrationId($registration){
        return self::where('description', $registration)->value('id');
    }
}