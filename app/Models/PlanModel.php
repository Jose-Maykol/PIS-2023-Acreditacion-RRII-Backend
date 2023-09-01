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
        'codigo',
        'nombre',
        'oportunidad_plan',
        'semestre_ejecucion',
        'duracion',
        'estado',
        'avance',
        'evaluacion_eficacia',

    ];


    public function getUser(){
        return $this->belongsTo(User::class,'id_user');
    }
    public function getStandard(){
        return $this->belongsTo(StandardModel::class,'id_standard');
    }
    public function getSources(){
        return $this->hasMany(SourceModel::class,'id_plan')->where('id_registration_status', Registration::select('id')->where('description', 'active'));
    }
    public function getGoals(){
        return $this->hasMany(GoalModel::class,'id_plan')->makehidden('updated_at');
    }
    public function getResources(){
        return $this->hasMany(ResourceModel::class,'id_plan');
    }
    public function getObservation(){
        return $this->hasMany(ObservationModel::class,'id_plan');
    }
    public function getProblemsOpportunities(){
        return $this->hasMany(ProblemsOpportunitiesModel::class,'id_plan');
    }
    public function getImprovementActions(){
        return $this->hasMany(ImprovementActionModel::class,'id_plan');
    }
    public function getRootCauses(){
        return $this->hasMany(RootCauseModel::class,'id_plan');
    }
    public function getResponsibles(){
        return $this->hasMany(ResponsibleModel::class,'id_plan');
    }
}
