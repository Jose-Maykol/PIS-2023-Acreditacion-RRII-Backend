<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvidenceModel extends Model {
    use HasFactory;
   
    public $timestamps = true;
    protected $table ='evidences';
    protected $fillable = [
        'folder_id',
        'file_id',
        'code',
        'standard_id',
        'evidence_type_id',
        'date_id',
    ];

    public function folder() {
        return $this->belongsTo(FolderModel::class, 'folder_id');
    }

    public function file() {
        return $this->belongsTo(FileModel::class, 'file_id');
    }

    public function evidenceType() {
        return $this->belongsTo(EvidenceTypeModel::class, 'evidence_type_id');
    }

    public function standard() {
        return $this->belongsTo(StandardModel::class, 'standard_id');
    }

    public function date() {
        return $this->belongsTo(DateModel::class, 'date_id');
    }
}