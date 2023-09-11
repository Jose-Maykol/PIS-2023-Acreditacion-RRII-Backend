<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourcesValues extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table ='sources_values';
    protected $fillable = [
        'value',
    ];
    
}
