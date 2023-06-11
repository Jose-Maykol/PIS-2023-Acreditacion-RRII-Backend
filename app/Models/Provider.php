<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;
	protected $fillable = [
        'provider',
		'id_provider',
		'avatar'
    ];

	protected $hidden = [
		'created_at',
		'updated_at'
	];

	public function users(){
        return $this->belongsTo(User::class,'id_user');
    }
}
