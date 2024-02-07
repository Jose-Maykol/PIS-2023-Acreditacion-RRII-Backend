<?php

namespace App\Exceptions\Plan;

use App\Exceptions\CustomNotFoundException;

class PlansNotFoundByDateException extends CustomNotFoundException
{
    protected $message = "No se encontró ningún plan de mejora en este periodo.";
}