<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolderModel extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $table = 'folders';
    protected $fillable = [
        'name',
        'path',
        'user_id',
        'parent_id',
        'plan_id',
        'evidence_type_id',
        'standard_id',
        'date_id'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function evidence()
    {
        return $this->hasOne(EvidenceModel::class, 'folder_id');
    }

    public function parent()
    {
        return $this->belongsTo(FolderModel::class, 'parent_id');
    }

    public function evidenceType()
    {
        return $this->belongsTo(EvidenceTypeModel::class, 'evidence_type_id');
    }

    public function standard()
    {
        return $this->belongsTo(StandardModel::class, 'standard_id');
    }

    public function date()
    {
        return $this->belongsTo(DateModel::class, 'date_id');
    }

    public function subfolders()
    {
        return $this->hasMany(FolderModel::class, 'parent_id');
    }

    public function files()
    {
        return $this->hasMany(FileModel::class, 'folder_id');
    }
}
