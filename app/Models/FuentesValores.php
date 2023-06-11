<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuentesValores extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table ='fuentes_valores';
    protected $fillable = [
        'valor',

    ];
}
