<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidence extends Model {
    use HasFactory;
    public $timestamps = true;
    protected $table ='evidences';
    protected $fillable = [
        'name',
        'path',
        'file',
        'type',
        'size',
        'user_id',
        'plan_id',
        'folder_id',
        'evidence_type_id',
        'standard_id',
        'date_id',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function folder() {
        return $this->belongsTo(Folder::class, 'folder_id');
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