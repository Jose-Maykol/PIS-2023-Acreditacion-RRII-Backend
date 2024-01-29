<?php

namespace App\Exceptions\Plan;

use App\Exceptions\CustomNotFoundException;

class PlanStatusNotFoundException extends CustomNotFoundException
{
    protected $message = "No se encontró el estado de plan de mejora.";
}