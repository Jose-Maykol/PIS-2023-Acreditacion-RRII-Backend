<?php

namespace App\Exceptions\Evidence;

use App\Exceptions\CustomNotFoundException;

class EvidenceNotFoundException
extends CustomNotFoundException
{
    protected $message = "No se encontró la evidencia.";
}
