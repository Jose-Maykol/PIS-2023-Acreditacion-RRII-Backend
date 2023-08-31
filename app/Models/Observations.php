<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observations extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table ='observations';
    protected $fillable = [
        'description',

    ];
    public function plans(){
        return $this->belongsTo(plan::class,'id_plan');
    }
}
