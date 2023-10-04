<?php

namespace App\Models;

use App\Traits\RegistrationStatusTrait;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanModel extends Model
{
    use HasFactory;
    use RegistrationStatusTrait;
    public $timestamps = true;

    protected $table = 'plans';
    protected $fillable = [
        'code',
        'name',
        'opportunity_for_improvement',
        'semester_execution',
        'advance',
        'duration',
        'efficacy_evaluation',
        'plan_status_id',
        'standard_id',
        'user_id',
        'date_id',
        'registration_status_id',
    ];

    public function date(): BelongsTo
    {
        return $this->belongsTo(DateModel::class, 'date_id');
    }
    public function registrationStatus(): BelongsTo
    {
        return $this->belongsTo(RegistrationStatus::class, 'registration_status_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function standard()
    {
        return $this->belongsTo(StandardModel::class, 'standard_id');
    }
    public function planStatus()
    {
        return $this->belongsTo(PlanStatusModel::class, 'plan_status_id');
    }
    public static function exists($plan_id)
    {
        return self::where($plan_id)->exists();
    }
    public function isActive()
    {
        return $this->where('registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->exists();
    }
    public static function existsAndActive($plan_id)
    {
        return self::where('id', $plan_id)->where('registration_status_id', RegistrationStatusModel::registrationActiveId())
            ->exists();
    }

    public function sources()
    {
        return $this->hasMany(SourceModel::class, 'plan_id');
    }

    public function problemsOpportunities()
    {
        return $this->hasMany(ProblemOpportunityModel::class, 'plan_id');
    }
   
    public function goals()
    {
        return $this->hasMany(GoalModel::class, 'plan_id');
    }
    
    public function resources()
    {
        return $this->hasMany(ResourceModel::class, 'plan_id');
    }
   
    public function observations()
    {
        return $this->hasMany(ObservationModel::class, 'plan_id');
    }

    public function improvementActions()
    {
        return $this->hasMany(ImprovementActionModel::class, 'plan_id');
    }
    
    public function rootCauses()
    {
        return $this->hasMany(RootCauseModel::class, 'plan_id');
    }

    public function responsibles()
    {
        return $this->hasMany(ResponsibleModel::class, 'plan_id');
    }
  
    public function isDate($year, $semester)
    {
        return $this->date()->where('year', $year)->where('semester', $semester)->exists();
    }

    public function deleteRegister()
    {
        return $this->update([
            'registration_status_id' => RegistrationStatusModel::registrationInactiveId()
        ]);
    }
}
