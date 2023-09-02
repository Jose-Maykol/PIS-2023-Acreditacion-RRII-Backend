<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStandardModel extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table ='users_standards';
    protected $fillable = [
        'date_id',
        'user_id',
        'standard_id'
    ];
}
