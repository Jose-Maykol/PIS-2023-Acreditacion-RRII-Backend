<?php

namespace App\Exceptions\Standard;

use App\Exceptions\CustomNotFoundException;

class StandardNotFoundException extends CustomNotFoundException
{
    protected $message = "No se encontró el estándar.";
}
