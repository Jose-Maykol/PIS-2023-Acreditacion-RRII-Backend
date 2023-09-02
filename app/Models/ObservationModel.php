<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObservationModel extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table ='observations';
    protected $fillable = [
        'description',
        'plan_id',
        'registration_status_id'
    ];
    public function plans(){
        return $this->belongsTo(PlanModel::class,'plan_id');
    }
}