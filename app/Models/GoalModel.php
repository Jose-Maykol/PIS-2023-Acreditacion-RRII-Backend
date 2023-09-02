<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoalModel extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table ='goals';
    protected $fillable = [
        'description',
        'plan_id',
        'registration_status_id'
    ];
    public function plans(){
        return $this->belongsTo(PlanModel::class,'plan_id');
    }
}
