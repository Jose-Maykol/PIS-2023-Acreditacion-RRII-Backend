<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvidenciasTipo extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table ='evidence_types';
    protected $fillable = [
        'description',
    ];
    public static function evidenceId($id){
        return self::where('id', $id)->value('description');
    }
}