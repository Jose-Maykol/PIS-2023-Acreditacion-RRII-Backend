<?php

namespace App\Exceptions\Plan;

use App\Exceptions\CustomNotFoundException;

class PlanNotFoundException extends CustomNotFoundException
{
    protected $message = "No se encontró el plan de mejora.";
}