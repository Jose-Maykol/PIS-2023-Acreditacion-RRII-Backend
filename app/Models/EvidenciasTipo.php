<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvidenciasTipo extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table ='evidencias_tipo';
    protected $fillable = [
        'tipo',
    ];
}