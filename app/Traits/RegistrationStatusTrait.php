<?php

namespace App\Traits;

use App\Models\RegistrationStatusModel;
use Illuminate\Database\Eloquent\Builder;

trait RegistrationStatusTrait
{
    public function scopeOnlyActive($query)
    {
        return $query->where('registration_status_id', RegistrationStatusModel::registrationActivo());
    }
}