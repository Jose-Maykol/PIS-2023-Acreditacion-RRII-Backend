<?php

namespace App\Models;

use App\Traits\RegistrationStatusTrait;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\RegistrationStatusModel;

class GoalModel extends Model
{
    use RegistrationStatusTrait, HasFactory ;
    
    public $timestamps = true;

    protected $table ='goals';
    protected $fillable = [
        'description',
        'plan_id'
    ];
    public function plan(): BelongsTo {
        return $this->belongsTo(PlanModel::class,'plan_id');
    }
}
