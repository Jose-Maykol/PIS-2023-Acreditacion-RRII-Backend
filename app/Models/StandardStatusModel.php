<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StandardStatusModel extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table ='standard_status';
    protected $fillable = [
        'description',
        'registration_status_id'
    ];
    public static function standardStatusId($standard_status){
        return self::where('description', $standard_status)->value('id');
    }

    public function deleteRegister(){
        return $this->update([
            'registration_status_id' => RegistrationStatusModel::registrationInactiveId()
        ]);
    }
}