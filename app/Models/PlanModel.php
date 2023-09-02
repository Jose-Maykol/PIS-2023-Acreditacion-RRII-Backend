<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanModel extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table ='plans';
    protected $fillable = [
        'code',
        'name',
        'opportunity_for_improvement',
        'semester_execution',
        'advance',
        'duration',
        'plan_status_id',
        'efficacy_evaluation',
        'standard_id',
        'user_id',
        'date_id',
        'registration_status_id',
    ];


    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function standard(){
        return $this->belongsTo(StandardModel::class,'standard_id');
    }
    public function planStatus(){
        return $this->belongsTo(PlanStatusModel::class,'plan_status_id');
    }
    public function sources(){
        return $this->hasMany(SourceModel::class,'plan_id')->where('registration_status_id', RegistrationStatusModel::select('id')->where('description', 'Active'));
    }
    public function goals(){
        return $this->hasMany(GoalModel::class,'plan_id');
    }
    public function resources(){
        return $this->hasMany(ResourceModel::class,'plan_id');
    }
    public function observations(){
        return $this->hasMany(ObservationModel::class,'plan_id');
    }
    public function problemsOpportunities(){
        return $this->hasMany(ProblemsOpportunitiesModel::class,'plan_id');
    }
    public function improvementActions(){
        return $this->hasMany(ImprovementActionModel::class,'plan_id');
    }
    public function rootCauses(){
        return $this->hasMany(RootCauseModel::class,'plan_id');
    }
    public function responsibles(){
        return $this->hasMany(ResponsibleModel::class,'plan_id');
    }
}
