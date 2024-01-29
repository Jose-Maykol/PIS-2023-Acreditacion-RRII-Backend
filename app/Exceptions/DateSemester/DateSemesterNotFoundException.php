<?php

namespace App\Exceptions\DateSemester;

use App\Exceptions\CustomNotFoundException;

class DateSemesterNotFoundException extends CustomNotFoundException
{
    protected $message = "No se encontró el periodo.";
}