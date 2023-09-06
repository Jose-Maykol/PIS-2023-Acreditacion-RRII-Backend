<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponsibleModel extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table ='responsibles';
    protected $fillable = [
        'name',
        'plan_id',
        'registration_status_id'
    ];
    public function plan(): BelongsTo {
        return $this->belongsTo(PlanModel::class,'plan_id');
    }
    public function registrationStatus(): BelongsTo{
        return $this->belongsTo(RegistrationStatus::class, 'registration_status_id');
    }
}
    

