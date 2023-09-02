<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function getStandards()
    {
        return $this->belongsTo(Estandar::class, 'standard_id');
    }

    

}
