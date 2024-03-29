<?php

namespace App\Exceptions\Plan;

use App\Exceptions\CustomNotFoundException;
use Exception;
use Throwable;

class PlansNotFoundByDateException extends Exception
{
    protected $message = "No se encontró ningún plan de mejora en este periodo.";
    protected $code = 404;

    public function __construct($message = null, $code = null, Throwable $previous = null)
    {
        if ($message !== null) {
            $this->message = $message;
        }

        if ($code !== null) {
            $this->code = $code;
        }

        parent::__construct($this->message, $this->code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'status' => 0,
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}