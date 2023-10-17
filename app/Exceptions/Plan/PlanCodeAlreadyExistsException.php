<?php

namespace App\Exceptions\Plan;

use Exception;
use Throwable;

class PlanCodeAlreadyExistsException extends Exception
{
    protected $message = "Código de plan de mejora ya existe";
    protected $code = 422;

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

