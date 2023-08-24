<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidencias extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table ='evidencias';
    protected $fillable = [
        'codigo',
        'denominacion',
        'adjunto',
    ];

    public function users(){
        return $this->belongsTo(User::class,'id_user');
    }
    public function plans(){
        return $this->belongsTo(plan::class,'id_plan');
    }
    public function evidenciaTipo(){
        return $this->belongsTo(EvidenciasTipo::class,'id_tipo');
    }
    public function evidenciaEstandar(){
        return $this->belongsTo(Estandar::class,'id_estandar');
    }
}
