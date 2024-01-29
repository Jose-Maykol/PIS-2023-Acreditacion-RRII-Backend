<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class CustomNotFoundException extends ModelNotFoundException
{
    protected $message = "El registro no existe";

    public function __construct($message = null, Throwable $previous = null)
    {
        parent::__construct();

        if ($message !== null) {
            $this->message = $message;
        }
    }

    public function render($request)
    {
        return response()->json([
            'status' => 0,
            'message' => $this->getMessage(),
        ], 404);
    }
}
