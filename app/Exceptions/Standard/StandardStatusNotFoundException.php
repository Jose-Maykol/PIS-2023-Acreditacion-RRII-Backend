<?php

namespace App\Exceptions\Standard;

use App\Exceptions\CustomNotFoundException;

class StandardStatusNotFoundException extends CustomNotFoundException
{
    protected $message = "No se encontró el estado de estándar.";
}