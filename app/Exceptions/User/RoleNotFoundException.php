<?php

namespace App\Exceptions\User;

use Exception;

class RoleNotFoundException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'status' => 0,
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}



