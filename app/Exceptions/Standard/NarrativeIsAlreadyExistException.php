<?php

namespace App\Exceptions\Standard;

use Exception;
use Throwable;

class NarrativeIsAlreadyExistException extends Exception
{
  protected $message = "La narrativa ya existe";
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
