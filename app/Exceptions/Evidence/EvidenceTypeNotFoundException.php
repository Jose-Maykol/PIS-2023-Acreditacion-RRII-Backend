<?php

namespace App\Exceptions\Evidence;

use App\Exceptions\CustomNotFoundException;

class EvidenceTypeNotFoundException
extends CustomNotFoundException
{
    protected $message = "No se encontró el tipo de evidencia.";
}