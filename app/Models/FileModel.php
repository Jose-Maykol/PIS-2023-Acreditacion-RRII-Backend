<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileModel extends Model {

    use HasFactory;
    
    public $timestamps = true;
    protected $table ='files';
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

    public function evidence() {
        return $this->hasOne(EvidenceModel::class, 'file_id', 'id');
    }

    public function folder() {
        return $this->belongsTo(FolderModel::class, 'folder_id');
    }

    public function evidenceType() {
        return $this->belongsTo(EvidenceTypeModel::class, 'evidence_type_id');
    }

    public function standard() {
        return $this->belongsTo(StandardModel::class, 'standard_id');
    }

    public function plan() {
        return $this->belongsTo(PlanModel::class, 'plan_id');
    }

    public function date() {
        return $this->belongsTo(DateModel::class, 'date_id');
    }
}