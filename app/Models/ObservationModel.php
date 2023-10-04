<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\RegistrationStatusModel;

class ObservationModel extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table ='observations';
    protected $fillable = [
        'description',
        'plan_id'
    ];
    public function plan(): BelongsTo {
        return $this->belongsTo(PlanModel::class,'plan_id');
    }
}
