<?php

namespace App\Exceptions\Evidence;

use App\Exceptions\CustomNotFoundException;

class FolderNotFoundException extends CustomNotFoundException
{
    protected $message = "No se encontró el folder.";
}