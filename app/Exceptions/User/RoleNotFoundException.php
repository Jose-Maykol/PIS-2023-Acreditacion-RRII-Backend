<?php

namespace App\Exceptions\User;

use App\Exceptions\CustomNotFoundException;

class RoleNotFoundException extends CustomNotFoundException
{
    protected $message = "No se encontró el rol.";
}



