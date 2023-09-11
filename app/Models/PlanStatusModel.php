<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanStatusModel extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table ='plan_status';
    protected $fillable = [
        'description',
        'registration_status_id'
    ];
    public static function planId($plan_status){
        return self::where('description', $plan_status)->value('id');
    }
    public function deleteRegister(){
        return $this->update([
            'registration_status_id' => RegistrationStatusModel::registrationDelete()
        ]);
    }
}