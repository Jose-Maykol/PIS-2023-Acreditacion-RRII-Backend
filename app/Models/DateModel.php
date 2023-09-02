<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DateModel extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table ='date_semesters';
    protected $fillable = [
        'year',
        'semester'
    ];
    
}
