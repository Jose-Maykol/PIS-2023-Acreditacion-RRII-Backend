<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NarrativeModel extends Model
{
    use HasFactory;
	public $timestamps = true;

    protected $table = 'narratives';
    protected $fillable = [
        'content',
        'date_id',
        'standard_id',
        'registration_status_id',
    ];

    public function standard(): BelongsTo{
        return $this->belongsTo(StandardModel::class, 'standard_id');
    }
    public function date(): BelongsTo{
        return $this->belongsTo(DateModel::class, 'date_id');
    }
    
    public function registrationStatus(): BelongsTo{
        return $this->belongsTo(RegistrationStatus::class, 'registration_status_id');
    }
    public function deleteRegister(){
        return $this->update([
            'registration_status_id' => RegistrationStatusModel::registrationDelete()
        ]);
    }
    

}
