<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImprovementActionModel extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table ='improvement_actions';
    protected $fillable = [
        'description',
        'plan_id',
        'registration_status_id'
    ];
    public function plans(){
        return $this->belongsTo(PlanModel::class,'plan_id');
    }
}
