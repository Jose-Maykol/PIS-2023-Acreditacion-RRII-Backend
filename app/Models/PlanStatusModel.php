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
    public static function planned(){
        return self::where('description', 'planificado')->value('id');
    }
}