<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use function PHPSTORM_META\map;

class StandardModel extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table ='standards';
    protected $fillable = [
        'name',
        'factor',
        'dimension',
        'related_standards',
        'nro_standard',
		'date_id',
        'registration_status_id'
    ];

    public function users(): BelongsToMany    {
        return $this->belongsToMany(User::class, 'users_standards', 'standard_id', 'user_id')
        ->using(UserStandardModel::class);
    }
    public function user($standard_id){
        return StandardModel::find($standard_id)->users()->first();
    }
    
    public function plans(){
        return $this->hasMany(PlanModel::class,'standard_id');
    }
	public function narratives(){
        return $this->hasMany(NarrativeModel::class,'standard_id');
    }
    public static function exists($standard_id){
        return self::where('id', $standard_id)->exists();
    }
    public static function isActive($standard_id){
        return self::where('id', $standard_id)->exists();
    }
    public static function existsAndActive($standard_id){
        return self::exists($standard_id) and self::isActive($standard_id);
    }
}
