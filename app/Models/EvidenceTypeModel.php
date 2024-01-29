<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvidenceTypeModel extends Model
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
    public static function getPlanificationId(){
        return self::where('description', 'planificación')->value('id');
    }
    public static function getResultId(){
        return self::where('description', 'resultado')->value('id');
    }
    public static function getImprovementId(){
        return self::where('description', 'mejora')->value('id');
    }

}