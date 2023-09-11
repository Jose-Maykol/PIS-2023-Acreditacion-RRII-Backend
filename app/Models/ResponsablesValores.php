<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponsablesValores extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table ='responsibles_values';
    protected $fillable = [
        'valor',

    ];
}
