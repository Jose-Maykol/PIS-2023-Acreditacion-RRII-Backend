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
        'size'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function folder() {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    public function evidenceType() {
        return $this->belongsTo(EvidenciasTipo::class, 'evidenceType_id');
    }

    public function standard() {
        return $this->belongsTo(Estandar::class, 'standard_id');
    }
}