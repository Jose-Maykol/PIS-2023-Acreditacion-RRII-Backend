<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Narrativa extends Model
{
    use HasFactory;

    protected $table = 'narrativas';
    protected $fillable = [
        'semester',
        'content',
        'registration',
    ];

    public function estandars()
    {
        return $this->belongsTo(Estandar::class, 'id_estandar');
    }

    

}
