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
        return ->select('id')->where('description', 'delete')->get();
    }
    public function registrationActivo(){
        return $this->select('id')->where('description', 'activo')->get();
    }
}//2023/A