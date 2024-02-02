<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot as RelationsPivot;

class UserStandardModel extends RelationsPivot
{
    use HasFactory;
    public $timestamps = false;

    protected $table ='users_standards';
    protected $fillable = [
        'user_id',
        'standard_id',
        'is_being_edited'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function standard()
    {
        return $this->belongsTo(StandardModel::class, 'standard_id');
    }
}
