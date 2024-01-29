<?php

namespace App\Exceptions\Evidence;

use Exception;
use Throwable;

class FolderCannotHaveEvidencesException extends Exception
{
    protected $message = "El folder no puede contener evidencias.";
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
