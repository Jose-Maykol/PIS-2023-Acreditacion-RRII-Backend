<?php

namespace App\Exceptions\Standard;

use Exception;
use Throwable;

class NarrativeIsEnabledException extends Exception
{
    protected $message = "La narrativa se encuentra habilidata, no puede puede realizar esta acción.";
    protected $code = 403;

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
