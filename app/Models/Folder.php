<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model 
{
    use HasFactory;
    
    public $timestamps = true;
    protected $table ='folders';
    protected $fillable = [
        'name',
        'path',
        'user_id',
        'parent_id',
        'evidence_type_id',  
        'standard_id',
        'date_id'
    ];

    public function users() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent() {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function evidenceType() {
        return $this->belongsTo(EvidenciasTipo::class, 'evidence_type_id');
    }

    public function standard() {
        return $this->belongsTo(Estandar::class, 'standard_id');
    }
    
    public function date() {
        return $this->belongsTo(DateModel::class, 'date_id');
    }
}