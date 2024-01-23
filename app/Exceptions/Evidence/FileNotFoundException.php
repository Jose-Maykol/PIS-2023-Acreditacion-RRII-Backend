<?php

namespace App\Exceptions\Evidence;

use App\Exceptions\CustomNotFoundException;

class FileNotFoundException extends CustomNotFoundException
{
    protected $message = "No se encontró el archivo.";
}