<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidencias extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table ='evidencias_tipo';
    protected $fillable = [
        'tipo',
    ];
}