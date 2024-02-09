<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DateModel extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table ='date_semesters';
    protected $fillable = [
        'year',
        'semester',
        'registration_status_id',
        'is_closed',
        'closing_date'
    ];
    public static function dateId($year, $semester){
        return self::where('year', $year)->where('semester', $semester)->value('id');
    }
    public static function exists($year, $semester){
        return self::where('year', $year)->where('semester', $semester)->exists();
    }
    public function deleteRegister(){
        return $this->update([
            'registration_status_id' => RegistrationStatusModel::registrationDelete()
        ]);
    }
}
