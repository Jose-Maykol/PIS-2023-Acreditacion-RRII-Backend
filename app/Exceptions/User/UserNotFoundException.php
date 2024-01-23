<?php

namespace App\Exceptions\User;

use App\Exceptions\CustomNotFoundException;


class UserNotFoundException extends CustomNotFoundException
{
    protected $message = "No se encontró el usuario.";
}
