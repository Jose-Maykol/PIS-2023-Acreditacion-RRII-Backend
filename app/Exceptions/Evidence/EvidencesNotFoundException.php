<?php

namespace App\Exceptions\Evidence;

use App\Exceptions\CustomNotFoundException;

class EvidencesNotFoundException
extends CustomNotFoundException
{
    protected $message = "No se encontraron evidencias.";
}