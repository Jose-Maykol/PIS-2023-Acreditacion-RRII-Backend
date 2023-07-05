<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadosValores extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table ='estados_valores';
    protected $fillable = [
        'valor',
    ];
}
