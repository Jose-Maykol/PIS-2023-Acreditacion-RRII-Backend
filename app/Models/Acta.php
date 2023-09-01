<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acta extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table = 'actas';
    protected $fillable = [
        'description',
		'date'        
    ];

    public function estandar()
    {
        return $this->belongsTo(Estandar::class, 'id_estandar');
    }
}
